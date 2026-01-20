// rules-engine.js - Business rules and logic

const RulesEngine = {
    // Valid status transitions for interns
    internStatusTransitions: {
        'ONBOARDING': ['ACTIVE'],
        'ACTIVE': ['EXITED'],
        'EXITED': [] // No transitions allowed from EXITED
    },
    
    // Valid status transitions for tasks
    taskStatusTransitions: {
        'PENDING': ['IN_PROGRESS', 'BLOCKED'],
        'IN_PROGRESS': ['DONE', 'BLOCKED'],
        'BLOCKED': ['IN_PROGRESS', 'PENDING'],
        'DONE': [] // Tasks cannot be moved from DONE
    },
    
    // Check if intern status transition is allowed
    canTransitionInternStatus(currentStatus, newStatus) {
        const allowedTransitions = this.internStatusTransitions[currentStatus] || [];
        
        if (!allowedTransitions.includes(newStatus)) {
            return {
                allowed: false,
                reason: `Cannot transition from ${currentStatus} to ${newStatus}`
            };
        }
        
        return { allowed: true };
    },
    
    // Check if task status change is allowed
    canChangeTaskStatus(task, newStatus) {
        // Cannot change status if task has unresolved dependencies
        if (newStatus === 'DONE' && task.dependencies && task.dependencies.length > 0) {
            const unresolvedDeps = task.dependencies.filter(depId => {
                const depTask = AppState.getTaskById(depId);
                return !depTask || depTask.status !== 'DONE';
            });
            
            if (unresolvedDeps.length > 0) {
                return {
                    allowed: false,
                    reason: `Cannot mark as DONE. Unresolved dependencies: ${unresolvedDeps.join(', ')}`
                };
            }
        }
        
        const allowedTransitions = this.taskStatusTransitions[task.status] || [];
        
        if (!allowedTransitions.includes(newStatus)) {
            return {
                allowed: false,
                reason: `Cannot transition from ${task.status} to ${newStatus}`
            };
        }
        
        return { allowed: true };
    },
    
    // Check if task can be assigned to intern
    canAssignTask(task, intern) {
        // Only ACTIVE interns can be assigned tasks
        if (intern.status !== 'ACTIVE') {
            return {
                allowed: false,
                reason: `Cannot assign task to ${intern.status} intern. Only ACTIVE interns can be assigned tasks.`
            };
        }
        
        // Check if intern has required skills
        const hasRequiredSkills = task.requiredSkills.every(reqSkill => 
            intern.skills.some(internSkill => 
                internSkill.toLowerCase() === reqSkill.toLowerCase()
            )
        );
        
        if (!hasRequiredSkills) {
            const missingSkills = task.requiredSkills.filter(reqSkill =>
                !intern.skills.some(internSkill => 
                    internSkill.toLowerCase() === reqSkill.toLowerCase()
                )
            );
            
            return {
                allowed: false,
                reason: `Intern missing required skills: ${missingSkills.join(', ')}`
            };
        }
        
        // Check if task is already assigned to this intern
        if (task.assignedTo === intern.id) {
            return {
                allowed: false,
                reason: 'Task is already assigned to this intern'
            };
        }
        
        return { allowed: true };
    },
    
    // Detect circular dependencies
    detectCircularDependencies(dependencies, taskId, visited = new Set()) {
        // If we're checking a new task (no taskId), just validate the dependencies
        if (!taskId) {
            // Check each dependency for circular refs within themselves
            for (const depId of dependencies) {
                const depTask = AppState.getTaskById(depId);
                if (depTask && depTask.dependencies) {
                    const check = this.detectCircularDependencies(
                        depTask.dependencies,
                        depId,
                        new Set()
                    );
                    if (!check.valid) {
                        return check;
                    }
                }
            }
            return { valid: true };
        }
        
        // Check for circular dependencies
        if (visited.has(taskId)) {
            return {
                valid: false,
                message: `Circular dependency detected involving task ${taskId}`
            };
        }
        
        visited.add(taskId);
        
        for (const depId of dependencies) {
            // Self-dependency check
            if (depId === taskId) {
                return {
                    valid: false,
                    message: 'Task cannot depend on itself'
                };
            }
            
            const depTask = AppState.getTaskById(depId);
            if (depTask && depTask.dependencies) {
                const result = this.detectCircularDependencies(
                    depTask.dependencies,
                    depId,
                    new Set(visited)
                );
                if (!result.valid) {
                    return result;
                }
            }
        }
        
        return { valid: true };
    },
    
    // Get eligible interns for a task
    getEligibleInterns(task) {
        return AppState.interns.filter(intern => {
            const canAssign = this.canAssignTask(task, intern);
            return canAssign.allowed;
        });
    },
    
    // Check if all dependencies are resolved
    areDependenciesResolved(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return true;
        }
        
        return task.dependencies.every(depId => {
            const depTask = AppState.getTaskById(depId);
            return depTask && depTask.status === 'DONE';
        });
    },
    
    // Get blocking dependencies for a task
    getBlockingDependencies(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return [];
        }
        
        return task.dependencies.filter(depId => {
            const depTask = AppState.getTaskById(depId);
            return !depTask || depTask.status !== 'DONE';
        });
    },
    
    // Auto-update task status based on dependencies
    autoUpdateTaskStatus(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return null; // No changes needed
        }
        
        const allResolved = this.areDependenciesResolved(task);
        
        // If all dependencies are resolved and task is blocked, unblock it
        if (allResolved && task.status === 'BLOCKED') {
            return task.assignedTo ? 'IN_PROGRESS' : 'PENDING';
        }
        
        // If dependencies are not resolved and task is not blocked, block it
        if (!allResolved && task.status !== 'BLOCKED' && task.status !== 'DONE') {
            return 'BLOCKED';
        }
        
        return null; // No changes needed
    },
    
    // Validate business rules before state changes
    validateBusinessRules(operation, data) {
        switch (operation) {
            case 'CREATE_INTERN':
                // No special business rules for creation
                return { valid: true };
                
            case 'UPDATE_INTERN_STATUS':
                return this.canTransitionInternStatus(data.currentStatus, data.newStatus);
                
            case 'ASSIGN_TASK':
                const task = AppState.getTaskById(data.taskId);
                const intern = AppState.getInternById(data.internId);
                if (!task || !intern) {
                    return { valid: false, reason: 'Task or Intern not found' };
                }
                return this.canAssignTask(task, intern);
                
            case 'UPDATE_TASK_STATUS':
                const taskToUpdate = AppState.getTaskById(data.taskId);
                if (!taskToUpdate) {
                    return { valid: false, reason: 'Task not found' };
                }
                return this.canChangeTaskStatus(taskToUpdate, data.newStatus);
                
            case 'CREATE_TASK':
                if (data.dependencies && data.dependencies.length > 0) {
                    return this.detectCircularDependencies(data.dependencies);
                }
                return { valid: true };
                
            default:
                return { valid: true };
        }
    }
};

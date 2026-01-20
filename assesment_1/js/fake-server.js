// fake-server.js - Simulates async server operations
// No real API calls - just simulated delays and responses

const FakeServer = {
    // Simulated delay range
    minDelay: 300,
    maxDelay: 800,
    
    // Simulate network delay
    async delay() {
        const ms = Math.random() * (this.maxDelay - this.minDelay) + this.minDelay;
        return new Promise(resolve => setTimeout(resolve, ms));
    },
    
    // Check if email is unique
    async checkEmailUniqueness(email, excludeId = null) {
        await this.delay();
        
        const exists = AppState.interns.some(intern => 
            intern.email.toLowerCase() === email.toLowerCase() && 
            intern.id !== excludeId
        );
        
        return {
            success: !exists,
            available: !exists,
            message: exists ? 'Email already exists' : 'Email is available'
        };
    },
    
    // Validate intern creation
    async validateInternCreation(internData) {
        await this.delay();
        
        const errors = [];
        
        // Check email uniqueness
        const emailCheck = await this.checkEmailUniqueness(internData.email);
        if (!emailCheck.available) {
            errors.push('Email already in use');
        }
        
        // Validate skills
        if (!internData.skills || internData.skills.length === 0) {
            errors.push('At least one skill is required');
        }
        
        return {
            success: errors.length === 0,
            errors
        };
    },
    
    // Create intern (simulated)
    async createIntern(internData) {
        await this.delay();
        
        const validation = await this.validateInternCreation(internData);
        if (!validation.success) {
            throw new Error(validation.errors.join(', '));
        }
        
        const intern = {
            id: AppState.generateInternId(),
            name: internData.name,
            email: internData.email,
            skills: internData.skills,
            status: 'ONBOARDING',
            createdAt: new Date().toISOString()
        };
        
        AppState.addIntern(intern);
        
        return {
            success: true,
            data: intern
        };
    },
    
    // Update intern (simulated)
    async updateIntern(id, updates) {
        await this.delay();
        
        const intern = AppState.getInternById(id);
        if (!intern) {
            throw new Error('Intern not found');
        }
        
        // If email is being updated, check uniqueness
        if (updates.email && updates.email !== intern.email) {
            const emailCheck = await this.checkEmailUniqueness(updates.email, id);
            if (!emailCheck.available) {
                throw new Error('Email already in use');
            }
        }
        
        AppState.updateIntern(id, updates);
        
        return {
            success: true,
            data: AppState.getInternById(id)
        };
    },
    
    // Change intern status (with business rules)
    async changeInternStatus(id, newStatus) {
        await this.delay();
        
        const intern = AppState.getInternById(id);
        if (!intern) {
            throw new Error('Intern not found');
        }
        
        // Validate status transition using rules engine
        const canTransition = RulesEngine.canTransitionInternStatus(intern.status, newStatus);
        if (!canTransition.allowed) {
            throw new Error(canTransition.reason);
        }
        
        AppState.updateIntern(id, { status: newStatus });
        
        return {
            success: true,
            data: AppState.getInternById(id)
        };
    },
    
    // Create task (simulated)
    async createTask(taskData) {
        await this.delay();
        
        // Validate dependencies
        if (taskData.dependencies && taskData.dependencies.length > 0) {
            const circularCheck = RulesEngine.detectCircularDependencies(
                taskData.dependencies,
                []
            );
            
            if (!circularCheck.valid) {
                throw new Error(circularCheck.message);
            }
        }
        
        const task = {
            id: AppState.generateTaskId(),
            title: taskData.title,
            description: taskData.description || '',
            requiredSkills: taskData.requiredSkills,
            estimatedHours: taskData.estimatedHours,
            dependencies: taskData.dependencies || [],
            assignedTo: null,
            status: 'PENDING',
            createdAt: new Date().toISOString()
        };
        
        AppState.addTask(task);
        
        return {
            success: true,
            data: task
        };
    },
    
    // Assign task to intern
    async assignTaskToIntern(taskId, internId) {
        await this.delay();
        
        const task = AppState.getTaskById(taskId);
        const intern = AppState.getInternById(internId);
        
        if (!task) {
            throw new Error('Task not found');
        }
        
        if (!intern) {
            throw new Error('Intern not found');
        }
        
        // Validate assignment using rules engine
        const canAssign = RulesEngine.canAssignTask(task, intern);
        if (!canAssign.allowed) {
            throw new Error(canAssign.reason);
        }
        
        AppState.assignTask(taskId, internId);
        
        // Check if dependencies are resolved and update status
        this.checkAndUpdateTaskStatus(taskId);
        
        return {
            success: true,
            data: AppState.getTaskById(taskId)
        };
    },
    
    // Update task status
    async updateTaskStatus(taskId, newStatus) {
        await this.delay();
        
        const task = AppState.getTaskById(taskId);
        if (!task) {
            throw new Error('Task not found');
        }
        
        // Validate status change
        const canChange = RulesEngine.canChangeTaskStatus(task, newStatus);
        if (!canChange.allowed) {
            throw new Error(canChange.reason);
        }
        
        AppState.updateTask(taskId, { status: newStatus });
        
        // If task is marked as DONE, check dependent tasks
        if (newStatus === 'DONE') {
            this.checkDependentTasks(taskId);
        }
        
        return {
            success: true,
            data: AppState.getTaskById(taskId)
        };
    },
    
    // Check and update task status based on dependencies
    checkAndUpdateTaskStatus(taskId) {
        const task = AppState.getTaskById(taskId);
        if (!task || !task.dependencies || task.dependencies.length === 0) {
            return;
        }
        
        const allDependenciesResolved = task.dependencies.every(depId => {
            const depTask = AppState.getTaskById(depId);
            return depTask && depTask.status === 'DONE';
        });
        
        if (!allDependenciesResolved && task.status !== 'BLOCKED') {
            AppState.updateTask(taskId, { status: 'BLOCKED' });
        } else if (allDependenciesResolved && task.status === 'BLOCKED') {
            AppState.updateTask(taskId, { 
                status: task.assignedTo ? 'IN_PROGRESS' : 'PENDING'
            });
        }
    },
    
    // Check tasks that depend on the completed task
    checkDependentTasks(completedTaskId) {
        AppState.tasks.forEach(task => {
            if (task.dependencies && task.dependencies.includes(completedTaskId)) {
                this.checkAndUpdateTaskStatus(task.id);
            }
        });
    },
    
    // Delete intern
    async deleteIntern(id) {
        await this.delay();
        
        const intern = AppState.getInternById(id);
        if (!intern) {
            throw new Error('Intern not found');
        }
        
        // Check if intern has assigned tasks
        const assignedTasks = AppState.tasks.filter(t => t.assignedTo === id);
        if (assignedTasks.length > 0) {
            throw new Error(`Cannot delete intern with ${assignedTasks.length} assigned task(s)`);
        }
        
        AppState.deleteIntern(id);
        
        return {
            success: true
        };
    },
    
    // Delete task
    async deleteTask(id) {
        await this.delay();
        
        const task = AppState.getTaskById(id);
        if (!task) {
            throw new Error('Task not found');
        }
        
        // Check if other tasks depend on this task
        const dependentTasks = AppState.tasks.filter(t => 
            t.dependencies && t.dependencies.includes(id)
        );
        
        if (dependentTasks.length > 0) {
            throw new Error(`Cannot delete task. ${dependentTasks.length} task(s) depend on it`);
        }
        
        AppState.deleteTask(id);
        
        return {
            success: true
        };
    }
};

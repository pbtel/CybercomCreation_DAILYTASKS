// state.js - Central State Management
// Single source of truth for the entire application

const AppState = {
    // Core data
    interns: [],
    tasks: [],
    logs: [],
    
    // UI state
    currentView: 'dashboard',
    filters: {
        status: '',
        skills: ''
    },
    
    // Counters for ID generation
    counters: {
        internSequence: 0,
        taskSequence: 0
    },
    
    // Initialize state
    init() {
        // Load from localStorage if available
        const saved = localStorage.getItem('intern-system-state');
        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                this.interns = parsed.interns || [];
                this.tasks = parsed.tasks || [];
                this.logs = parsed.logs || [];
                this.counters = parsed.counters || { internSequence: 0, taskSequence: 0 };
            } catch (e) {
                console.error('Failed to load state:', e);
            }
        }
        
        this.addLog('System', 'Application initialized');
    },
    
    // Save state to localStorage
    save() {
        try {
            const stateToSave = {
                interns: this.interns,
                tasks: this.tasks,
                logs: this.logs,
                counters: this.counters
            };
            localStorage.setItem('intern-system-state', JSON.stringify(stateToSave));
        } catch (e) {
            console.error('Failed to save state:', e);
        }
    },
    
    // Intern operations
    addIntern(intern) {
        this.interns.push(intern);
        this.save();
        this.addLog('Intern Created', `${intern.name} (${intern.id}) added to system`);
    },
    
    updateIntern(id, updates) {
        const intern = this.interns.find(i => i.id === id);
        if (intern) {
            Object.assign(intern, updates);
            this.save();
            this.addLog('Intern Updated', `${intern.name} (${id}) updated`);
        }
    },
    
    deleteIntern(id) {
        const intern = this.interns.find(i => i.id === id);
        if (intern) {
            this.interns = this.interns.filter(i => i.id !== id);
            // Unassign all tasks for this intern
            this.tasks.forEach(task => {
                if (task.assignedTo === id) {
                    task.assignedTo = null;
                    task.status = 'PENDING';
                }
            });
            this.save();
            this.addLog('Intern Deleted', `${intern.name} (${id}) removed from system`);
        }
    },
    
    getInternById(id) {
        return this.interns.find(i => i.id === id);
    },
    
    // Task operations
    addTask(task) {
        this.tasks.push(task);
        this.save();
        this.addLog('Task Created', `${task.title} (${task.id}) created`);
    },
    
    updateTask(id, updates) {
        const task = this.tasks.find(t => t.id === id);
        if (task) {
            Object.assign(task, updates);
            this.save();
            this.addLog('Task Updated', `${task.title} (${id}) updated`);
        }
    },
    
    deleteTask(id) {
        const task = this.tasks.find(t => t.id === id);
        if (task) {
            this.tasks = this.tasks.filter(t => t.id !== id);
            // Remove this task from dependencies of other tasks
            this.tasks.forEach(t => {
                if (t.dependencies && t.dependencies.includes(id)) {
                    t.dependencies = t.dependencies.filter(dep => dep !== id);
                }
            });
            this.save();
            this.addLog('Task Deleted', `${task.title} (${id}) removed from system`);
        }
    },
    
    getTaskById(id) {
        return this.tasks.find(t => t.id === id);
    },
    
    assignTask(taskId, internId) {
        const task = this.getTaskById(taskId);
        const intern = this.getInternById(internId);
        if (task && intern) {
            task.assignedTo = internId;
            task.status = 'IN_PROGRESS';
            this.save();
            this.addLog('Task Assigned', `${task.title} assigned to ${intern.name}`);
        }
    },
    
    unassignTask(taskId) {
        const task = this.getTaskById(taskId);
        if (task) {
            const intern = this.getInternById(task.assignedTo);
            task.assignedTo = null;
            task.status = 'PENDING';
            this.save();
            this.addLog('Task Unassigned', `${task.title} unassigned from ${intern ? intern.name : 'unknown'}`);
        }
    },
    
    // Log operations
    addLog(action, details, type = 'info') {
        const log = {
            id: `LOG-${Date.now()}`,
            timestamp: new Date().toISOString(),
            action,
            details,
            type
        };
        this.logs.unshift(log); // Add to beginning
        
        // Keep only last 100 logs
        if (this.logs.length > 100) {
            this.logs = this.logs.slice(0, 100);
        }
        
        this.save();
    },
    
    clearLogs() {
        this.logs = [];
        this.save();
        this.addLog('System', 'Logs cleared');
    },
    
    // ID generation
    generateInternId() {
        const year = new Date().getFullYear();
        this.counters.internSequence++;
        const sequence = String(this.counters.internSequence).padStart(3, '0');
        this.save();
        return `INT-${year}-${sequence}`;
    },
    
    generateTaskId() {
        this.counters.taskSequence++;
        const sequence = String(this.counters.taskSequence).padStart(3, '0');
        this.save();
        return `TASK-${sequence}`;
    },
    
    // Computed values
    getActiveInterns() {
        return this.interns.filter(i => i.status === 'ACTIVE');
    },
    
    getInternTaskCount(internId) {
        return this.tasks.filter(t => t.assignedTo === internId).length;
    },
    
    getTotalEstimatedHours() {
        return this.tasks.reduce((sum, task) => sum + (task.estimatedHours || 0), 0);
    },
    
    getFilteredInterns() {
        let filtered = [...this.interns];
        
        if (this.filters.status) {
            filtered = filtered.filter(i => i.status === this.filters.status);
        }
        
        if (this.filters.skills) {
            const searchSkills = this.filters.skills.toLowerCase().trim();
            filtered = filtered.filter(i => 
                i.skills.some(skill => skill.toLowerCase().includes(searchSkills))
            );
        }
        
        return filtered;
    },
    
    // Statistics
    getStats() {
        return {
            totalInterns: this.interns.length,
            activeInterns: this.interns.filter(i => i.status === 'ACTIVE').length,
            totalTasks: this.tasks.length,
            pendingTasks: this.tasks.filter(t => t.status === 'PENDING').length,
            inProgressTasks: this.tasks.filter(t => t.status === 'IN_PROGRESS').length,
            doneTasks: this.tasks.filter(t => t.status === 'DONE').length
        };
    },
    
    // Reset all data (for testing)
    reset() {
        this.interns = [];
        this.tasks = [];
        this.logs = [];
        this.counters = { internSequence: 0, taskSequence: 0 };
        this.filters = { status: '', skills: '' };
        localStorage.removeItem('intern-system-state');
        this.addLog('System', 'All data reset');
    }
};

// Initialize on load
AppState.init();

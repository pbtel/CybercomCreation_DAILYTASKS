// app.js - Application bootstrap and event handling

const App = {
    currentEditingIntern: null,
    currentAssigningTask: null,
    
    // Initialize application
    init() {
        this.setupNavigation();
        this.setupModals();
        this.setupInternForm();
        this.setupTaskForm();
        this.setupFilters();
        this.setupErrorToast();
        this.setupLogs();
        
        // Initial render
        Renderer.updateAllViews();
        
        console.log('Intern Operations System initialized');
    },
    
    // Setup navigation
    setupNavigation() {
        const navButtons = document.querySelectorAll('.nav-btn');
        navButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view;
                this.navigateTo(view);
            });
        });
    },
    
    // Navigate to view
    navigateTo(viewName) {
        // Update nav buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === viewName) {
                btn.classList.add('active');
            }
        });
        
        // Update views
        document.querySelectorAll('.view').forEach(view => {
            view.classList.remove('active');
        });
        
        const targetView = document.getElementById(`${viewName}-view`);
        if (targetView) {
            targetView.classList.add('active');
            AppState.currentView = viewName;
            
            // Refresh data for the view
            if (viewName === 'dashboard') {
                Renderer.renderDashboard();
            } else if (viewName === 'interns') {
                Renderer.renderInternsTable();
            } else if (viewName === 'tasks') {
                Renderer.renderTasksTable();
            } else if (viewName === 'logs') {
                Renderer.renderLogs();
            }
        }
    },
    
    // Setup modals
    setupModals() {
        // Close modal buttons
        document.querySelectorAll('.close-modal, .cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.closeAllModals();
            });
        });
        
        // Click outside to close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeAllModals();
                }
            });
        });
        
        // Add intern button
        document.getElementById('add-intern-btn').addEventListener('click', () => {
            this.openInternModal();
        });
        
        // Add task button
        document.getElementById('add-task-btn').addEventListener('click', () => {
            this.openTaskModal();
        });
    },
    
    // Setup intern form
    setupInternForm() {
        const form = document.getElementById('intern-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleInternSubmit();
        });
    },
    
    // Setup task form
    setupTaskForm() {
        const form = document.getElementById('task-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleTaskSubmit();
        });
        
        // Assign task confirmation
        document.getElementById('confirm-assign-btn').addEventListener('click', async () => {
            await this.handleTaskAssignment();
        });
    },
    
    // Setup filters
    setupFilters() {
        const statusFilter = document.getElementById('status-filter');
        const skillFilter = document.getElementById('skill-filter');
        const clearBtn = document.getElementById('clear-filters-btn');
        
        statusFilter.addEventListener('change', () => {
            AppState.filters.status = statusFilter.value;
            Renderer.renderInternsTable();
        });
        
        skillFilter.addEventListener('input', () => {
            AppState.filters.skills = skillFilter.value;
            Renderer.renderInternsTable();
        });
        
        clearBtn.addEventListener('click', () => {
            statusFilter.value = '';
            skillFilter.value = '';
            AppState.filters.status = '';
            AppState.filters.skills = '';
            Renderer.renderInternsTable();
        });
    },
    
    // Setup error toast
    setupErrorToast() {
        const closeBtn = document.querySelector('#error-toast .close-btn');
        closeBtn.addEventListener('click', () => {
            Renderer.hideError();
        });
    },
    
    // Setup logs
    setupLogs() {
        document.getElementById('clear-logs-btn').addEventListener('click', () => {
            if (confirm('Are you sure you want to clear all logs?')) {
                AppState.clearLogs();
                Renderer.renderLogs();
            }
        });
    },
    
    // Open intern modal
    openInternModal(internId = null) {
        const modal = document.getElementById('intern-modal');
        const form = document.getElementById('intern-form');
        const title = document.getElementById('intern-modal-title');
        
        form.reset();
        Validators.clearFormErrors('intern-form');
        
        if (internId) {
            const intern = AppState.getInternById(internId);
            if (intern) {
                title.textContent = 'Edit Intern';
                document.getElementById('intern-name').value = intern.name;
                document.getElementById('intern-email').value = intern.email;
                document.getElementById('intern-skills').value = intern.skills.join(', ');
                this.currentEditingIntern = internId;
            }
        } else {
            title.textContent = 'Add Intern';
            this.currentEditingIntern = null;
        }
        
        modal.classList.remove('hidden');
    },
    
    // Open task modal
    openTaskModal(taskId = null) {
        const modal = document.getElementById('task-modal');
        const form = document.getElementById('task-form');
        const title = document.getElementById('task-modal-title');
        
        form.reset();
        Validators.clearFormErrors('task-form');
        
        title.textContent = 'Create Task';
        modal.classList.remove('hidden');
    },
    
    // Open assign task modal
    openAssignTaskModal(taskId) {
        this.currentAssigningTask = taskId;
        const modal = document.getElementById('assign-task-modal');
        Renderer.populateAssignTaskModal(taskId);
        modal.classList.remove('hidden');
    },
    
    // Close all modals
    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        this.currentEditingIntern = null;
        this.currentAssigningTask = null;
    },
    
    // Handle intern form submit
    async handleInternSubmit() {
        const formData = {
            name: document.getElementById('intern-name').value,
            email: document.getElementById('intern-email').value,
            skills: document.getElementById('intern-skills').value
        };
        
        // Validate form
        const validation = Validators.validateInternForm(formData);
        if (!validation.valid) {
            Validators.displayFormErrors(validation.errors, 'intern-form');
            return;
        }
        
        try {
            Renderer.showLoading();
            
            // Create intern via fake server
            const result = await FakeServer.createIntern(validation.data);
            
            this.closeAllModals();
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Handle task form submit
    async handleTaskSubmit() {
        const formData = {
            title: document.getElementById('task-title').value,
            description: document.getElementById('task-description').value,
            requiredSkills: document.getElementById('task-skills').value,
            estimatedHours: document.getElementById('task-hours').value,
            dependencies: document.getElementById('task-dependencies').value
        };
        
        // Validate form
        const validation = Validators.validateTaskForm(formData);
        if (!validation.valid) {
            Validators.displayFormErrors(validation.errors, 'task-form');
            return;
        }
        
        try {
            Renderer.showLoading();
            
            // Create task via fake server
            const result = await FakeServer.createTask(validation.data);
            
            this.closeAllModals();
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Handle task assignment
    async handleTaskAssignment() {
        const internId = document.getElementById('assign-intern-select').value;
        
        if (!internId) {
            Renderer.showError('Please select an intern');
            return;
        }
        
        try {
            Renderer.showLoading();
            
            await FakeServer.assignTaskToIntern(this.currentAssigningTask, internId);
            
            this.closeAllModals();
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Activate intern
    async activateIntern(internId) {
        if (!confirm('Activate this intern?')) return;
        
        try {
            Renderer.showLoading();
            
            await FakeServer.changeInternStatus(internId, 'ACTIVE');
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Exit intern
    async exitIntern(internId) {
        if (!confirm('Mark this intern as exited?')) return;
        
        try {
            Renderer.showLoading();
            
            await FakeServer.changeInternStatus(internId, 'EXITED');
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Delete intern
    async deleteIntern(internId) {
        const intern = AppState.getInternById(internId);
        if (!confirm(`Delete intern ${intern.name}? This action cannot be undone.`)) return;
        
        try {
            Renderer.showLoading();
            
            await FakeServer.deleteIntern(internId);
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Unassign task
    async unassignTask(taskId) {
        if (!confirm('Unassign this task?')) return;
        
        try {
            Renderer.showLoading();
            
            AppState.unassignTask(taskId);
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Complete task
    async completeTask(taskId) {
        if (!confirm('Mark this task as complete?')) return;
        
        try {
            Renderer.showLoading();
            
            await FakeServer.updateTaskStatus(taskId, 'DONE');
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },
    
    // Delete task
    async deleteTask(taskId) {
        const task = AppState.getTaskById(taskId);
        if (!confirm(`Delete task "${task.title}"? This action cannot be undone.`)) return;
        
        try {
            Renderer.showLoading();
            
            await FakeServer.deleteTask(taskId);
            
            Renderer.updateAllViews();
            
        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    }
};

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

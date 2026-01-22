// renderer.js - DOM updates and rendering

const Renderer = {
    // Show loading overlay
    showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    },
    
    // Hide loading overlay
    hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
    },
    
    // Show error toast
    showError(message) {
        const toast = document.getElementById('error-toast');
        const messageEl = toast.querySelector('.error-message');
        messageEl.textContent = message;
        toast.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000);
    },
    
    // Hide error toast
    hideError() {
        document.getElementById('error-toast').classList.add('hidden');
    },
    
    // Render dashboard
    renderDashboard() {
        const stats = AppState.getStats();
        
        // Update stats
        document.getElementById('total-interns').textContent = stats.totalInterns;
        document.getElementById('active-interns').textContent = stats.activeInterns;
        document.getElementById('total-tasks').textContent = stats.totalTasks;
        document.getElementById('pending-tasks').textContent = stats.pendingTasks;
        
        // Render recent interns
        this.renderRecentInterns();
        
        // Render recent tasks
        this.renderRecentTasks();
    },
    
    // Render recent interns
    renderRecentInterns() {
        const container = document.getElementById('recent-interns-list');
        const recentInterns = AppState.interns.slice(-5).reverse();
        
        if (recentInterns.length === 0) {
            container.innerHTML = '<p class="empty-state">No interns yet</p>';
            return;
        }
        
        container.innerHTML = recentInterns.map(intern => `
            <div class="recent-item">
                <div class="recent-item-info">
                    <h4>${this.escapeHtml(intern.name)}</h4>
                    <p>${this.escapeHtml(intern.email)} • ${this.getBadge(intern.status, 'status')}</p>
                </div>
                <span class="badge badge-${intern.status.toLowerCase()}">${intern.status}</span>
            </div>
        `).join('');
    },
    
    // Render recent tasks
    renderRecentTasks() {
        const container = document.getElementById('recent-tasks-list');
        const recentTasks = AppState.tasks.slice(-5).reverse();
        
        if (recentTasks.length === 0) {
            container.innerHTML = '<p class="empty-state">No tasks yet</p>';
            return;
        }
        
        container.innerHTML = recentTasks.map(task => {
            const intern = task.assignedTo ? AppState.getInternById(task.assignedTo) : null;
            return `
                <div class="recent-item">
                    <div class="recent-item-info">
                        <h4>${this.escapeHtml(task.title)}</h4>
                        <p>${intern ? this.escapeHtml(intern.name) : 'Unassigned'} • ${task.estimatedHours}h</p>
                    </div>
                    <span class="badge badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span>
                </div>
            `;
        }).join('');
    },
    
    // Render interns table
    renderInternsTable() {
        const tbody = document.getElementById('interns-table-body');
        const interns = AppState.getFilteredInterns();
        
        if (interns.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-state">No interns found</td></tr>';
            return;
        }
        
        tbody.innerHTML = interns.map(intern => {
            const taskCount = AppState.getInternTaskCount(intern.id);
            return `
                <tr>
                    <td><strong>${this.escapeHtml(intern.id)}</strong></td>
                    <td>${this.escapeHtml(intern.name)}</td>
                    <td>${this.escapeHtml(intern.email)}</td>
                    <td>
                        <div class="skills-container">
                            ${intern.skills.map(skill => 
                                `<span class="skill-tag">${this.escapeHtml(skill)}</span>`
                            ).join('')}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-${intern.status.toLowerCase()}">${intern.status}</span>
                    </td>
                    <td><strong>${taskCount}</strong></td>
                    <td>
                        <div class="action-btns">
                            ${this.getInternActionButtons(intern)}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    },
    
    // Get action buttons for intern based on status
    getInternActionButtons(intern) {
        let buttons = '';
        
        if (intern.status === 'ONBOARDING') {
            buttons += `<button class="btn btn-success btn-sm" onclick="App.activateIntern('${intern.id}')">Activate</button>`;
        }
        
        if (intern.status === 'ACTIVE') {
            buttons += `<button class="btn btn-warning btn-sm" onclick="App.exitIntern('${intern.id}')">Exit</button>`;
        }
        
        const taskCount = AppState.getInternTaskCount(intern.id);
        if (taskCount === 0) {
            buttons += `<button class="btn btn-danger btn-sm" onclick="App.deleteIntern('${intern.id}')">Delete</button>`;
        }
        
        return buttons;
    },
    
    // Render tasks table
    renderTasksTable() {
        const tbody = document.getElementById('tasks-table-body');
        const tasks = AppState.tasks;
        
        if (tasks.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No tasks found</td></tr>';
            return;
        }
        
        tbody.innerHTML = tasks.map(task => {
            const intern = task.assignedTo ? AppState.getInternById(task.assignedTo) : null;
            return `
                <tr>
                    <td><strong>${this.escapeHtml(task.id)}</strong></td>
                    <td>${this.escapeHtml(task.title)}</td>
                    <td>
                        <div class="skills-container">
                            ${task.requiredSkills.map(skill => 
                                `<span class="skill-tag">${this.escapeHtml(skill)}</span>`
                            ).join('')}
                        </div>
                    </td>
                    <td>${intern ? this.escapeHtml(intern.name) : '<em>Unassigned</em>'}</td>
                    <td>
                        <span class="badge badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span>
                    </td>
                    <td><strong>${task.estimatedHours}h</strong></td>
                    <td>
                        ${this.renderDependencies(task)}
                    </td>
                    <td>
                        <div class="action-btns">
                            ${this.getTaskActionButtons(task)}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
        // Update total hours
        document.getElementById('total-hours').textContent = AppState.getTotalEstimatedHours();
    },
    
    // Render task dependencies
    renderDependencies(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return '<em>None</em>';
        }
        
        return `
            <div class="dependencies-list">
                ${task.dependencies.map(depId => {
                    const depTask = AppState.getTaskById(depId);
                    const isDone = depTask && depTask.status === 'DONE';
                    return `<span class="dependency-tag ${isDone ? 'done' : ''}">${this.escapeHtml(depId)}</span>`;
                }).join('')}
            </div>
        `;
    },
    
    // Get action buttons for task
    getTaskActionButtons(task) {
        let buttons = '';
        
        if (!task.assignedTo && task.status !== 'DONE') {
            buttons += `<button class="btn btn-info btn-sm" onclick="App.openAssignTaskModal('${task.id}')">Assign</button>`;
        }
        
        if (task.assignedTo && task.status !== 'DONE') {
            buttons += `<button class="btn btn-secondary btn-sm" onclick="App.unassignTask('${task.id}')">Unassign</button>`;
        }
        
        if (task.status === 'IN_PROGRESS') {
            const canComplete = RulesEngine.areDependenciesResolved(task);
            buttons += `<button class="btn btn-success btn-sm" ${!canComplete ? 'disabled' : ''} onclick="App.completeTask('${task.id}')">Complete</button>`;
        }
        
        // Can only delete if no other tasks depend on it
        const hasDependents = AppState.tasks.some(t => 
            t.dependencies && t.dependencies.includes(task.id)
        );
        
        if (!hasDependents) {
            buttons += `<button class="btn btn-danger btn-sm" onclick="App.deleteTask('${task.id}')">Delete</button>`;
        }
        
        return buttons;
    },
    
    // Render logs
    renderLogs() {
        const container = document.getElementById('logs-list');
        const logs = AppState.logs;
        
        if (logs.length === 0) {
            container.innerHTML = `
                <div class="empty-logs">
                    <p>No activity logs yet</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = logs.map(log => `
            <div class="log-entry ${log.type}">
                <div class="log-time">${this.formatDate(log.timestamp)}</div>
                <div class="log-action">${this.escapeHtml(log.action)}</div>
                <div class="log-details">${this.escapeHtml(log.details)}</div>
            </div>
        `).join('');
    },
    
    // Populate assign task modal
    populateAssignTaskModal(taskId) {
        const task = AppState.getTaskById(taskId);
        if (!task) return;
        
        const info = document.getElementById('assign-task-info');
        info.textContent = `Assign "${task.title}" to an intern`;
        
        const select = document.getElementById('assign-intern-select');
        const eligibleInterns = RulesEngine.getEligibleInterns(task);
        
        if (eligibleInterns.length === 0) {
            select.innerHTML = '<option value="">No eligible interns available</option>';
            document.getElementById('confirm-assign-btn').disabled = true;
            return;
        }
        
        select.innerHTML = '<option value="">-- Select an intern --</option>' +
            eligibleInterns.map(intern => 
                `<option value="${intern.id}">${this.escapeHtml(intern.name)} (${this.escapeHtml(intern.id)})</option>`
            ).join('');
        
        document.getElementById('confirm-assign-btn').disabled = false;
    },
    
    // Utility: Get badge HTML
    getBadge(value, type) {
        const className = `badge-${value.toLowerCase().replace('_', '-')}`;
        return `<span class="badge ${className}">${value}</span>`;
    },
    
    // Utility: Format date
    formatDate(isoString) {
        const date = new Date(isoString);
        return date.toLocaleString();
    },
    
    // Utility: Escape HTML
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    // Update all views
    updateAllViews() {
        this.renderDashboard();
        this.renderInternsTable();
        this.renderTasksTable();
        this.renderLogs();
    }
};

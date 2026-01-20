// validators.js - Form and data validation

const Validators = {
    // Email validation
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || email.trim() === '') {
            return { valid: false, message: 'Email is required' };
        }
        if (!emailRegex.test(email)) {
            return { valid: false, message: 'Invalid email format' };
        }
        return { valid: true };
    },
    
    // Name validation
    validateName(name) {
        if (!name || name.trim() === '') {
            return { valid: false, message: 'Name is required' };
        }
        if (name.trim().length < 2) {
            return { valid: false, message: 'Name must be at least 2 characters' };
        }
        if (name.trim().length > 100) {
            return { valid: false, message: 'Name must be less than 100 characters' };
        }
        return { valid: true };
    },
    
    // Skills validation
    validateSkills(skillsString) {
        if (!skillsString || skillsString.trim() === '') {
            return { valid: false, message: 'At least one skill is required' };
        }
        
        const skills = skillsString.split(',').map(s => s.trim()).filter(s => s);
        
        if (skills.length === 0) {
            return { valid: false, message: 'At least one skill is required' };
        }
        
        // Check for duplicates
        const uniqueSkills = new Set(skills.map(s => s.toLowerCase()));
        if (uniqueSkills.size !== skills.length) {
            return { valid: false, message: 'Duplicate skills found' };
        }
        
        return { valid: true, skills };
    },
    
    // Task title validation
    validateTaskTitle(title) {
        if (!title || title.trim() === '') {
            return { valid: false, message: 'Title is required' };
        }
        if (title.trim().length < 3) {
            return { valid: false, message: 'Title must be at least 3 characters' };
        }
        if (title.trim().length > 200) {
            return { valid: false, message: 'Title must be less than 200 characters' };
        }
        return { valid: true };
    },
    
    // Estimated hours validation
    validateEstimatedHours(hours) {
        const num = parseInt(hours);
        if (isNaN(num)) {
            return { valid: false, message: 'Hours must be a number' };
        }
        if (num < 1) {
            return { valid: false, message: 'Hours must be at least 1' };
        }
        if (num > 1000) {
            return { valid: false, message: 'Hours must be less than 1000' };
        }
        return { valid: true, value: num };
    },
    
    // Dependencies validation
    validateDependencies(dependenciesString) {
        if (!dependenciesString || dependenciesString.trim() === '') {
            return { valid: true, dependencies: [] };
        }
        
        const dependencies = dependenciesString
            .split(',')
            .map(d => d.trim())
            .filter(d => d);
        
        // Check if all dependencies exist
        const invalidDeps = dependencies.filter(depId => {
            return !AppState.getTaskById(depId);
        });
        
        if (invalidDeps.length > 0) {
            return { 
                valid: false, 
                message: `Invalid task IDs: ${invalidDeps.join(', ')}` 
            };
        }
        
        // Check for duplicates
        const uniqueDeps = new Set(dependencies);
        if (uniqueDeps.size !== dependencies.length) {
            return { valid: false, message: 'Duplicate dependencies found' };
        }
        
        return { valid: true, dependencies };
    },
    
    // Validate intern form
    validateInternForm(formData) {
        const errors = {};
        
        const nameValidation = this.validateName(formData.name);
        if (!nameValidation.valid) {
            errors.name = nameValidation.message;
        }
        
        const emailValidation = this.validateEmail(formData.email);
        if (!emailValidation.valid) {
            errors.email = emailValidation.message;
        }
        
        const skillsValidation = this.validateSkills(formData.skills);
        if (!skillsValidation.valid) {
            errors.skills = skillsValidation.message;
        }
        
        return {
            valid: Object.keys(errors).length === 0,
            errors,
            data: {
                name: formData.name.trim(),
                email: formData.email.trim(),
                skills: skillsValidation.skills || []
            }
        };
    },
    
    // Validate task form
    validateTaskForm(formData) {
        const errors = {};
        
        const titleValidation = this.validateTaskTitle(formData.title);
        if (!titleValidation.valid) {
            errors.title = titleValidation.message;
        }
        
        const skillsValidation = this.validateSkills(formData.requiredSkills);
        if (!skillsValidation.valid) {
            errors.requiredSkills = skillsValidation.message;
        }
        
        const hoursValidation = this.validateEstimatedHours(formData.estimatedHours);
        if (!hoursValidation.valid) {
            errors.estimatedHours = hoursValidation.message;
        }
        
        const depsValidation = this.validateDependencies(formData.dependencies);
        if (!depsValidation.valid) {
            errors.dependencies = depsValidation.message;
        }
        
        return {
            valid: Object.keys(errors).length === 0,
            errors,
            data: {
                title: formData.title.trim(),
                description: formData.description ? formData.description.trim() : '',
                requiredSkills: skillsValidation.skills || [],
                estimatedHours: hoursValidation.value || 0,
                dependencies: depsValidation.dependencies || []
            }
        };
    },
    
    // Display form errors
    displayFormErrors(errors, formId) {
        // Clear all previous errors
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.querySelectorAll('.field-error').forEach(el => {
            el.textContent = '';
        });
        
        // Display new errors
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`#${formId.replace('-form', '')}-${fieldName}`);
            if (field) {
                const errorSpan = field.parentElement.querySelector('.field-error');
                if (errorSpan) {
                    errorSpan.textContent = errors[fieldName];
                }
                field.classList.add('error');
            }
        });
    },
    
    // Clear form errors
    clearFormErrors(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        form.querySelectorAll('.field-error').forEach(el => {
            el.textContent = '';
        });
        
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('error');
        });
    }
};

import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        pattern: String,
        message: String,
        min: Number,
        required: Boolean
    }

    static targets = ["input", "error"]

    connect() {
        this.element.addEventListener('input', this.validate.bind(this));
        this.element.addEventListener('blur', this.validate.bind(this));
    }

    validate(event) {
        const input = event.target;
        const value = input.value;
        let isValid = true;
        let currentMessage = "";

        if (this.hasRequiredValue && !value) {
            isValid = false;
            currentMessage = "This field is required";
        } else if (this.hasMinValue && value.length < this.minValue && value.length > 0) {
            isValid = false;
            currentMessage = `Minimum ${this.minValue} characters required`;
        } else if (this.hasPatternValue && value.length > 0) {
            const regex = new RegExp(this.patternValue);
            if (!regex.test(value)) {
                isValid = false;
                currentMessage = this.messageValue || "Invalid format";
            }
        }

        this.updateUI(input, isValid, currentMessage);
    }

    updateUI(input, isValid, message) {
        // Find or create error element
        let errorElement = this.element.querySelector('.live-error-msg');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'live-error-msg text-red-500 text-[10px] font-black uppercase tracking-wider px-1 mt-1';
            this.element.appendChild(errorElement);
        }

        if (isValid) {
            input.classList.remove('bg-red-50', 'border-red-200');
            input.classList.add('bg-slate-50', 'border-slate-100');
            errorElement.textContent = '';
            
            // Hide the Symfony backend error if it exists
            const backendError = this.element.querySelector('ul');
            if (backendError) backendError.style.display = 'none';
        } else {
            input.classList.add('bg-red-50', 'border-red-200');
            input.classList.remove('bg-slate-50', 'border-slate-100');
            errorElement.textContent = message;
        }
    }
}

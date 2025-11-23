/**
 * Validation & Dialog System
 * Custom form validation và dialog component theo Shadcn UI
 */

// ============= VALIDATION =============

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
    }

    // Validate required fields
    validateRequired(fieldId, message = "Trường này là bắt buộc") {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();

        if (!value) {
            this.addError(fieldId, message);
            return false;
        }

        this.removeError(fieldId);
        return true;
    }

    // Validate email
    validateEmail(fieldId, message = "Email không hợp lệ") {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (value && !emailRegex.test(value)) {
            this.addError(fieldId, message);
            return false;
        }

        this.removeError(fieldId);
        return true;
    }

    // Validate min length
    validateMinLength(fieldId, minLength, message = null) {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();

        if (value && value.length < minLength) {
            this.addError(fieldId, message || `Tối thiểu ${minLength} ký tự`);
            return false;
        }

        this.removeError(fieldId);
        return true;
    }

    // Validate max length
    validateMaxLength(fieldId, maxLength, message = null) {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();

        if (value && value.length > maxLength) {
            this.addError(fieldId, message || `Tối đa ${maxLength} ký tự`);
            return false;
        }

        this.removeError(fieldId);
        return true;
    }

    // Validate number
    validateNumber(fieldId, message = "Phải là số") {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();

        if (value && isNaN(value)) {
            this.addError(fieldId, message);
            return false;
        }

        this.removeError(fieldId);
        return true;
    }

    // Add error to field
    addError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const formGroup = field.closest(".form-group");

        // Add error class
        field.classList.add("error");

        // Remove existing error message
        const existingError = formGroup.querySelector(".form-error");
        if (existingError) {
            existingError.remove();
        }

        // Add error message
        const errorElement = document.createElement("span");
        errorElement.className = "form-error";
        errorElement.textContent = message;
        field.parentNode.insertBefore(errorElement, field.nextSibling);

        this.errors[fieldId] = message;
    }

    // Remove error from field
    removeError(fieldId) {
        const field = document.getElementById(fieldId);
        const formGroup = field.closest(".form-group");

        field.classList.remove("error");

        const errorElement = formGroup.querySelector(".form-error");
        if (errorElement) {
            errorElement.remove();
        }

        delete this.errors[fieldId];
    }

    // Check if form has errors
    hasErrors() {
        return Object.keys(this.errors).length > 0;
    }

    // Get all errors
    getErrors() {
        return this.errors;
    }

    // Clear all errors
    clearErrors() {
        Object.keys(this.errors).forEach((fieldId) => {
            this.removeError(fieldId);
        });
        this.errors = {};
    }
}

// ============= DIALOG SYSTEM =============

class Dialog {
    constructor() {
        this.createDialogElement();
    }

    createDialogElement() {
        // Check if dialog already exists
        if (document.getElementById("app-dialog")) {
            return;
        }

        const overlay = document.createElement("div");
        overlay.id = "app-dialog";
        overlay.className = "dialog-overlay hidden";
        overlay.innerHTML = `
            <div class="dialog" onclick="event.stopPropagation()">
                <div class="dialog-header">
                    <h3 class="dialog-title" id="dialog-title">
                        <i class="bi bi-info-circle"></i>
                        <span id="dialog-title-text">Thông báo</span>
                    </h3>
                </div>
                <div class="dialog-body" id="dialog-body">
                    Nội dung thông báo
                </div>
                <div class="dialog-footer" id="dialog-footer">
                    <button class="btn btn-primary" id="dialog-ok-btn">OK</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Click overlay to close
        overlay.addEventListener("click", () => {
            this.close();
        });
    }

    show(options = {}) {
        const {
            title = "Thông báo",
            message = "",
            type = "info", // info, success, error, warning, confirm
            icon = "bi-info-circle",
            onConfirm = null,
            onCancel = null,
            confirmText = "OK",
            cancelText = "Hủy",
        } = options;

        const overlay = document.getElementById("app-dialog");
        const titleElement = document.getElementById("dialog-title-text");
        const bodyElement = document.getElementById("dialog-body");
        const footerElement = document.getElementById("dialog-footer");
        const iconElement = overlay.querySelector(".dialog-title i");

        // Set title
        titleElement.textContent = title;

        // Set icon
        iconElement.className = `bi ${icon}`;

        // Set message
        bodyElement.innerHTML = message;

        // Set footer buttons
        if (type === "confirm") {
            footerElement.innerHTML = `
                <button class="btn btn-outline" id="dialog-cancel-btn">${cancelText}</button>
                <button class="btn btn-primary" id="dialog-ok-btn">${confirmText}</button>
            `;

            document
                .getElementById("dialog-cancel-btn")
                .addEventListener("click", () => {
                    this.close();
                    if (onCancel) onCancel();
                });

            document
                .getElementById("dialog-ok-btn")
                .addEventListener("click", () => {
                    this.close();
                    if (onConfirm) onConfirm();
                });
        } else {
            footerElement.innerHTML = `
                <button class="btn btn-primary" id="dialog-ok-btn">${confirmText}</button>
            `;

            document
                .getElementById("dialog-ok-btn")
                .addEventListener("click", () => {
                    this.close();
                    if (onConfirm) onConfirm();
                });
        }

        // Show dialog
        overlay.classList.remove("hidden");
    }

    close() {
        const overlay = document.getElementById("app-dialog");
        overlay.classList.add("hidden");
    }

    // Shorthand methods
    info(message, title = "Thông báo") {
        this.show({
            type: "info",
            title,
            message,
            icon: "bi-info-circle",
        });
    }

    success(message, title = "Thành công") {
        this.show({
            type: "success",
            title,
            message,
            icon: "bi-check-circle",
        });
    }

    error(message, title = "Lỗi") {
        this.show({
            type: "error",
            title,
            message,
            icon: "bi-exclamation-circle",
        });
    }

    warning(message, title = "Cảnh báo") {
        this.show({
            type: "warning",
            title,
            message,
            icon: "bi-exclamation-triangle",
        });
    }

    confirm(message, onConfirm, options = {}) {
        this.show({
            type: "confirm",
            message,
            onConfirm,
            icon: "bi-question-circle",
            ...options,
        });
    }
}

// Create global dialog instance
const dialog = new Dialog();

// Export for use
window.FormValidator = FormValidator;
window.dialog = dialog;

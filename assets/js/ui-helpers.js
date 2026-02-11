
let currentToast = null;

// 1. Toast Notification (Top-Center)
function showToast(message, type = 'info', description = '', linkUrl = null) {
    if (currentToast) {
        currentToast.hideToast();
    }

    const icons = {
        success: `<i class="bi bi-check-circle-fill text-green-500 text-lg"></i>`,
        error: `<i class="bi bi-x-circle-fill text-red-500 text-lg"></i>`,
        info: `<i class="bi bi-info-circle-fill text-blue-500 text-lg"></i>`,
        warning: `<i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg"></i>`
    };

    const toastNode = document.createElement('div');
    toastNode.className = 'flex items-start';

    const iconContainer = document.createElement('div');
    iconContainer.className = 'mr-3 flex-shrink-0';
    iconContainer.innerHTML = icons[type] || icons['info'];

    const textContainer = document.createElement('div');
    // Added 'break-words' to handle long text within the fixed width
    textContainer.className = 'overflow-hidden'; 

    const textElement = document.createElement('div');
    textElement.textContent = message;
    textElement.className = 'text-sm font-bold text-gray-900 leading-tight truncate';
    textContainer.appendChild(textElement);

    if (description && description.trim() !== '') {
        const descElement = document.createElement('div');
        descElement.textContent = description;
        descElement.className = 'text-xs text-gray-500 mt-1 leading-relaxed line-clamp-2';
        textContainer.appendChild(descElement);
    }

    toastNode.appendChild(iconContainer);
    toastNode.appendChild(textContainer);

    currentToast = Toastify({
        node: toastNode,
        duration: 4000,
        gravity: "top",
        position: "center",
        stopOnFocus: true,
        style: {
            background: "#ffffff",
            color: "#1f2937",
            boxShadow: "0 10px 15px -3px rgba(0, 0, 0, 0.1)",
            borderRadius: "8px",
            border: "1px solid #e5e7eb",
            padding: "14px 20px",
            width: "fit-content",
            maxWidth: "90vw",
        }
    });
    currentToast.showToast();
}

// 2. Confirmation Modal (Yes/No)
function showConfirmation(message, description = '', onYes = null, onNo = null) {
    const modal = document.getElementById("confirmModal");
    const msgEl = document.getElementById("confirmMessage");
    const descEl = document.getElementById("confirmDescription");
    const yesBtn = document.getElementById("yesBtn");
    const noBtn = document.getElementById("noBtn");

    msgEl.textContent = message;
    descEl.textContent = description;
    descEl.classList.toggle("hidden", description.trim() === '');

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    yesBtn.onclick = () => {
        modal.classList.replace("flex", "hidden");
        if (onYes) onYes();
    };

    noBtn.onclick = () => {
        modal.classList.replace("flex", "hidden");
        if (onNo) onNo();
    };
}

// 3. OK Modal
function showOkModal(message, description = '') {
    const modal = document.getElementById("okConfirmModal");
    const msgEl = modal.querySelector("#confirmMessage");
    const descEl = modal.querySelector("#confirmDescription");
    const okBtn = modal.querySelector("#yesBtn");

    msgEl.textContent = message;
    descEl.textContent = description;
    descEl.classList.toggle("hidden", description.trim() === '');

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    okBtn.onclick = () => {
        modal.classList.replace("flex", "hidden");
    };
}
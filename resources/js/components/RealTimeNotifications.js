export class RealTimeNotifications {
    constructor() {
        this.notificationSound = new Audio('/notification.mp3');
        this.init();
    }

    init() {
        this.listenForWorkPermitUpdates();
        this.listenForApprovalAssignments();
        this.listenForPersonalNotifications();
    }

    listenForWorkPermitUpdates() {
        const companyId = document.querySelector('meta[name="company-id"]')?.content;

        if (companyId) {
            window.Echo.private(`company.${companyId}`)
                .listen('admin.work-permit.updated', (e) => {
                    this.showWorkPermitUpdate(e);
                })
                .error((error) => {
                    console.error('Work permit updates channel error:', error);
                });
        }
    }

    listenForApprovalAssignments() {
        const userId = document.querySelector('meta[name="user-id"]')?.content;

        if (userId) {
            window.Echo.private(`user.${userId}`)
                .listen('.approval.assigned', (e) => {
                    this.showApprovalAssignment(e);
                })
                .error((error) => {
                    console.error('Approval assignments channel error:', error);
                });
        }
    }

    listenForPersonalNotifications() {
        const userId = document.querySelector('meta[name="user-id"]')?.content;

        if (userId) {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    this.showPersonalNotification(notification);
                })
                .error((error) => {
                    console.error('Personal notifications channel error:', error);
                });
        }
    }

    showWorkPermitUpdate(data) {
        this.playNotificationSound();
        this.showToast({
            title: 'İş İzni Güncellemesi',
            message: data.message,
            type: this.getNotificationType(data.action),
            action: () => {
                window.location.href = `/work-permits/${data.work_permit_id}`;
            }
        });
    }

    showApprovalAssignment(data) {
        this.playNotificationSound();
        this.showToast({
            title: 'Yeni Onay Talebi',
            message: `${data.title} - ${data.company_name}`,
            type: 'warning',
            action: () => {
                window.location.href = data.url;
            }
        });
    }

    showPersonalNotification(notification) {
        this.playNotificationSound();
        this.showToast({
            title: 'Bildirim',
            message: notification.message,
            type: this.getNotificationType(notification.action_type),
            action: () => {
                window.location.href = notification.url;
            }
        });
    }

    getNotificationType(action) {
        const types = {
            'created': 'info',
            'approved': 'success',
            'rejected': 'error',
            'completed': 'success',
            'pending_approval': 'warning'
        };
        return types[action] || 'info';
    }

    playNotificationSound() {
        if (document.hidden) {
            this.notificationSound.play().catch(() => {
                // Ses çalınamazsa sessiz devam et
            });
        }
    }

    showToast({ title, message, type = 'info', action = null }) {
        this.createToastElement({ title, message, type, action });
    }

    createToastElement({ title, message, type, action }) {
        const toast = document.createElement('div');

        // Border renkleri için class mapping
        const borderColors = {
            'success': 'border-emerald-500',
            'error': 'border-rose-500',
            'warning': 'border-amber-500',
            'info': 'border-blue-500'
        };

        toast.className = `fixed top-4 right-4 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 ${
            borderColors[type] || 'border-blue-500'
        } z-50 transform transition-transform duration-300 translate-x-full`;

        toast.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="shrink-0">
                        ${this.getIcon(type)}
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">${this.escapeHtml(title)}</p>
                        <p class="mt-1 text-sm text-gray-500">${this.escapeHtml(message)}</p>
                        ${action ? `
                            <div class="mt-3 flex space-x-3">
                                <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 view-button">
                                    İncele
                                </button>
                                <button type="button" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-500 close-button">
                                    Kapat
                                </button>
                            </div>
                        ` : `
                            <button type="button" class="mt-2 inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-500 close-button">
                                Kapat
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        // Animasyon
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full');
        });

        // Event listeners
        this.setupToastEventListeners(toast, action);

        // Otomatik kaldırma
        this.setupAutoRemove(toast);
    }

    setupToastEventListeners(toast, action) {
        if (action) {
            const viewButton = toast.querySelector('.view-button');
            viewButton.addEventListener('click', () => {
                action();
                this.removeToast(toast);
            });
        }

        const closeButton = toast.querySelector('.close-button');
        closeButton.addEventListener('click', () => {
            this.removeToast(toast);
        });

        // Toast'a tıklanınca da kapat
        toast.addEventListener('click', (e) => {
            if (e.target === toast) {
                this.removeToast(toast);
            }
        });
    }

    setupAutoRemove(toast) {
        const removeTimeout = setTimeout(() => {
            if (document.body.contains(toast)) {
                this.removeToast(toast);
            }
        }, 8000);

        // Toast kaldırıldığında timeout'u temizle
        toast.dataset.removeTimeout = removeTimeout;
    }

    removeToast(toast) {
        if (toast.dataset.removeTimeout) {
            clearTimeout(parseInt(toast.dataset.removeTimeout));
        }

        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }

    getIcon(type) {
        const icons = {
            success: `
                <svg class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `,
            error: `
                <svg class="h-6 w-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `,
            warning: `
                <svg class="h-6 w-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            `,
            info: `
                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            `
        };
        return icons[type] || icons.info;
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Bağlantı kesildiğinde yeniden bağlanma
    setupReconnection() {
        window.Echo.connector.socket.on('disconnect', () => {
            console.log('WebSocket disconnected, attempting to reconnect...');
        });

        window.Echo.connector.socket.on('reconnect', () => {
            console.log('WebSocket reconnected, reinitializing listeners...');
            this.init();
        });
    }

    // Temizleme metodu (component destroy edilirken çağrılmalı)
    destroy() {
        // Tüm dinleyicileri temizle
        if (window.Echo) {
            window.Echo.leaveAllChannels();
        }

        // Tüm toast'ları temizle
        document.querySelectorAll('[class*="translate-x-full"]').forEach(toast => {
            toast.remove();
        });
    }
}

class EventBookingApp {
    constructor() {
        this.apiUrl = '/api';
        this.token = localStorage.getItem('token');
        this.user = null;
        this.init();
    }

    init() {
        this.bindEvents();
        if (this.token) {
            this.loadUser();
        }
    }

    bindEvents() {
        // Auth buttons
        document.getElementById('login-btn').onclick = () => this.showLogin();
        document.getElementById('register-btn').onclick = () => this.showRegister();
        document.getElementById('logout-btn').onclick = () => this.logout();

        // Forms
        document.getElementById('login').onsubmit = (e) => this.handleLogin(e);
        document.getElementById('register').onsubmit = (e) => this.handleRegister(e);
        document.getElementById('event-form').onsubmit = (e) => this.handleCreateEvent(e);

        // Modals
        document.getElementById('create-event-btn').onclick = () => this.showEventModal();
        document.getElementById('cancel-event').onclick = () => this.hideEventModal();

        // Search
        document.getElementById('search-events').oninput = (e) => this.searchEvents(e.target.value);
    }

    async apiCall(endpoint, options = {}) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...(this.token && { 'Authorization': `Bearer ${this.token}` })
            },
            ...options
        };

        const response = await fetch(`${this.apiUrl}${endpoint}`, config);
        const data = await response.json();
        
        if (!response.ok) throw new Error(data.message || 'API Error');
        return data;
    }

    showLogin() {
        document.getElementById('login-form').classList.remove('hidden');
        document.getElementById('register-form').classList.add('hidden');
    }

    showRegister() {
        document.getElementById('register-form').classList.remove('hidden');
        document.getElementById('login-form').classList.add('hidden');
    }

    async handleLogin(e) {
        e.preventDefault();
        try {
            const data = await this.apiCall('/login', {
                method: 'POST',
                body: JSON.stringify({
                    email: document.getElementById('login-email').value,
                    password: document.getElementById('login-password').value
                })
            });
            
            this.token = data.token;
            localStorage.setItem('token', this.token);
            this.loadUser();
        } catch (error) {
            this.showError(error.message || 'Login failed');
        }
    }

    async handleRegister(e) {
        e.preventDefault();
        try {
            const data = await this.apiCall('/register', {
                method: 'POST',
                body: JSON.stringify({
                    name: document.getElementById('register-name').value,
                    email: document.getElementById('register-email').value,
                    password: document.getElementById('register-password').value,
                    password_confirmation: document.getElementById('register-password-confirmation').value,
                    phone_number: document.getElementById('register-phone').value,
                    role: document.getElementById('register-role').value
                })
            });
            
            this.token = data.token;
            localStorage.setItem('token', this.token);
            this.loadUser();
        } catch (error) {
            this.showError(error.message || 'Registration failed');
        }
    }

    async loadUser() {
        try {
            this.user = await this.apiCall('/me');
            this.showMainContent();
            this.loadEvents();
            if (this.user.role === 'customer') {
                this.loadBookings();
            }
        } catch (error) {
            this.logout();
        }
    }

    showMainContent() {
        document.getElementById('auth-section').classList.add('hidden');
        document.getElementById('main-content').classList.remove('hidden');
        document.getElementById('nav-links').innerHTML = '';
        
        document.getElementById('user-name').textContent = this.user.name;
        document.getElementById('user-role').textContent = `(${this.user.role})`;
        
        if (this.user.role === 'organizer') {
            document.getElementById('create-event-btn').classList.remove('hidden');
        } else {
            document.getElementById('bookings-section').classList.remove('hidden');
        }
    }

    async logout() {
        try {
            await this.apiCall('/logout', { method: 'POST' });
        } catch (error) {}
        
        localStorage.removeItem('token');
        this.token = null;
        this.user = null;
        location.reload();
    }

    async loadEvents() {
        try {
            const events = await this.apiCall('/events');
            this.renderEvents(events.data || events);
        } catch (error) {
            console.error('Failed to load events:', error);
        }
    }

    renderEvents(events) {
        const container = document.getElementById('events-list');
        container.innerHTML = events.map(event => `
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-xl font-bold">${event.name}</h3>
                <p class="text-gray-600 mb-2">${event.description}</p>
                <p class="text-sm text-gray-500">📅 ${new Date(event.date).toLocaleDateString()}</p>
                <p class="text-sm text-gray-500">📍 ${event.location}</p>
                <div class="mt-4 flex gap-2">
                    <button onclick="app.viewEvent(${event.id})" class="bg-blue-500 text-white px-3 py-1 rounded text-sm">View</button>
                    ${this.user?.role === 'organizer' ? `
                        <button onclick="app.editEvent(${event.id})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</button>
                        <button onclick="app.deleteEvent(${event.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Delete</button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    async loadBookings() {
        try {
            const bookings = await this.apiCall('/bookings');
            this.renderBookings(bookings.data || bookings);
        } catch (error) {
            console.error('Failed to load bookings:', error);
        }
    }

    renderBookings(bookings) {
        const container = document.getElementById('bookings-list');
        container.innerHTML = bookings.map(booking => `
            <div class="bg-white p-4 rounded shadow">
                <h4 class="font-bold">${booking.event?.name || 'Event'}</h4>
                <p class="text-sm text-gray-600">Status: ${booking.status}</p>
                <p class="text-sm text-gray-600">Quantity: ${booking.quantity}</p>
                <div class="mt-2">
                    ${booking.status !== 'cancelled' ? `
                        <button onclick="app.cancelBooking(${booking.id})" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Cancel</button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    showEventModal() {
        document.getElementById('event-modal').classList.remove('hidden');
        document.getElementById('event-modal').classList.add('flex');
    }

    hideEventModal() {
        document.getElementById('event-modal').classList.add('hidden');
        document.getElementById('event-modal').classList.remove('flex');
    }

    async handleCreateEvent(e) {
        e.preventDefault();
        try {
            await this.apiCall('/events', {
                method: 'POST',
                body: JSON.stringify({
                    name: document.getElementById('event-name').value,
                    description: document.getElementById('event-description').value,
                    date: document.getElementById('event-date').value,
                    location: document.getElementById('event-location').value
                })
            });
            
            this.hideEventModal();
            document.getElementById('event-form').reset();
            this.loadEvents();
        } catch (error) {
            alert(error.message);
        }
    }

    async viewEvent(id) {
        try {
            const event = await this.apiCall(`/events/${id}`);
            alert(`Event: ${event.name}\n${event.description}\nTickets available: ${event.tickets?.length || 0}`);
        } catch (error) {
            alert(error.message);
        }
    }

    async deleteEvent(id) {
        if (confirm('Delete this event?')) {
            try {
                await this.apiCall(`/events/${id}`, { method: 'DELETE' });
                this.loadEvents();
            } catch (error) {
                alert(error.message);
            }
        }
    }

    async cancelBooking(id) {
        if (confirm('Cancel this booking?')) {
            try {
                await this.apiCall(`/bookings/${id}/cancel`, { method: 'PUT' });
                this.loadBookings();
            } catch (error) {
                alert(error.message);
            }
        }
    }

    searchEvents(query) {
        // Simple client-side search - in production, this should be server-side
        const events = document.querySelectorAll('#events-list > div');
        events.forEach(event => {
            const text = event.textContent.toLowerCase();
            event.style.display = text.includes(query.toLowerCase()) ? 'block' : 'none';
        });
    }
}

    showError(message) {
        let errorDiv = document.getElementById('error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'error-message';
            errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
            document.getElementById('auth-section').prepend(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
        
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }
}

// Initialize app
const app = new EventBookingApp();
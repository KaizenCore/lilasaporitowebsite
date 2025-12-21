import Alpine from 'alpinejs'

// Calendar Quick View Component
Alpine.data('calendarQuickView', () => ({
    showModal: false,
    loading: false,
    classData: null,

    async openQuickView(classId) {
        this.showModal = true
        this.loading = true
        this.classData = null

        try {
            const response = await fetch(`/admin/calendar/class/${classId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })

            if (!response.ok) {
                throw new Error('Failed to fetch class data')
            }

            this.classData = await response.json()
        } catch (error) {
            console.error('Failed to load class data:', error)
            alert('Failed to load class details. Please try again.')
            this.closeModal()
        } finally {
            this.loading = false
        }
    },

    closeModal() {
        this.showModal = false
        setTimeout(() => {
            this.classData = null
            this.loading = false
        }, 300) // Wait for fade out animation
    },

    formatDate(dateString) {
        if (!dateString) return ''

        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        })
    }
}))

// Make openQuickView globally accessible for event cards
window.openQuickView = (classId) => {
    // Find the Alpine component instance and call its method
    const modalElement = document.querySelector('[x-data="calendarQuickView()"]')
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data.openQuickView(classId)
    } else {
        console.error('Calendar quick view component not found')
    }
}

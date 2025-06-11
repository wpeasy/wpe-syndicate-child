document.addEventListener('alpine:init', () => {
    Alpine.data('syndicateChildSettings', () => ({
        url: '',
        status: '',
        uuid: document.getElementById('wpe-child-uuid').value || '',
        connected: false,

        init() {
            const stored = wp_localStorage.getItem('wpe_syndicate_child') || '{}';
            const data = JSON.parse(stored);
            this.url = data.url || '';

            if (this.uuid) {
                this.connected = true;
                this.status = 'Connected';
            } else {
                this.status = 'Not Connected';
            }
        },

        get buttonLabel() {
            return this.connected ? 'Disconnect' : 'Connect to Master';
        },

        async toggleConnection() {
            const endpoint = this.connected ? 'disconnect' : 'connect';
            const res = await fetch(`${this.url}/wp-json/wpe-syndicate-master/v1/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': wpeSyndicateChild.nonce
                },
                body: JSON.stringify({ url: window.location.origin })
            });

            const json = await res.json();
            if (res.ok && json.uuid) {
                this.connected = true;
                this.uuid = json.uuid;
                this.status = 'Connected';
                wp_localStorage.setItem('wpe_syndicate_child', JSON.stringify({ url: this.url, uuid: this.uuid }));
            } else {
                this.connected = false;
                this.uuid = '';
                this.status = 'Not Connected';
            }
        }
    }));
});

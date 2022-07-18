export const notifications = {
    methods: {
        showError: function (value) {
            if (typeof value === 'object' && value.status == 422) {
                const first = Object.keys(value.data.errors)[0];
                value = value.data.errors[first][0];
            }
            if (typeof value === 'undefined') {
                value = 'An error has occurred';
            }
            this.$notify({
                message: value,
                type: 'error',
                top: false,
                bottom: true,
                left: false,
                right: true,
                showClose: false,
                closeDelay: 4000,
            });
        },
        showSuccess: function (value = 'success') {
            this.$notify({
                message: value,
                type: 'success',
                top: false,
                bottom: true,
                left: false,
                right: true,
                showClose: false,
                closeDelay: 4000,
            });
        },
        showAlert(value) {
            this.$notify({
                message: value,
                type: 'warning',
                top: false,
                bottom: true,
                left: false,
                right: true,
                showClose: false,
                closeDelay: 6000,
            });
        },

        showAlert1() {
            this.$notify({
                message: "you must configure your TMDB ( to able to fetch movies info and langs ) api key in settings",
                type: 'warning',
                top: false,
                bottom: true,
                left: false,
                right: true,
                showClose: false,
                closeDelay: 6000,
            });
        },
        showConfirm: function (value) {
            Vue.dialog
                .confirm('Please confirm to continue')
                .then(function (dialog) {
                    value();
                })
                .catch(function () {
                });
        },

    },
};

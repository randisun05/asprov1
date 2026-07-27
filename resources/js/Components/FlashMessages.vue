<template>
    <!-- No markup of its own - just watches session flash props and pops a SweetAlert. -->
</template>

<script>
import Swal from 'sweetalert2';

export default {
    name: 'FlashMessages',

    watch: {
        '$page.props.session': {
            handler(session) {
                if (!session) return;

                if (session.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: session.success,
                        timer: 3000,
                        showConfirmButton: false,
                    });
                }

                if (session.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: session.error,
                    });
                }

                if (session.info) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: session.info,
                        timer: 3500,
                        showConfirmButton: false,
                    });
                }

                if (session.warning) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: session.warning,
                        timer: 4000,
                        showConfirmButton: false,
                    });
                }

                if (session.status) {
                    Swal.fire({
                        icon: 'info',
                        text: session.status,
                        timer: 3000,
                        showConfirmButton: false,
                    });
                }
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>

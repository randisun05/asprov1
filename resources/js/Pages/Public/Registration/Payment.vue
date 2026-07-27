<template>

    <Head>
        <title>Pembayaran Registrasi</title>
    </Head>

    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><h3>Pembayaran Registrasi</h3></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="registration">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 col-md-10 col-sm-12 text-center">
                    <p class="mb-4">Klik tombol di bawah untuk membayar biaya keanggotaan atas nama <strong>{{ register.name }}</strong> menggunakan Midtrans.</p>
                    <button class="btn btn-primary btn-lg" @click="pay" :disabled="paying">
                        {{ paying ? 'Memproses…' : 'Bayar Sekarang' }}
                    </button>
                </div>
            </div>
        </div>
    </section>

</template>

<script>
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import LayoutWebsite from '../../../Layouts/Website.vue';

export default {
    layout: LayoutWebsite,
    components: { Head },
    props: {
        register: Object,
        snapToken: String,
        clientKey: String,
        isProduction: Boolean,
    },
    data() {
        return { paying: false };
    },
    mounted() {
        const script = document.createElement('script');
        script.src = this.isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', this.clientKey);
        document.head.appendChild(script);
    },
    methods: {
        pay() {
            this.paying = true;
            window.snap.pay(this.snapToken, {
                onSuccess: () => {
                    Inertia.visit(`/registration/success`);
                },
                onPending: () => {
                    Inertia.visit(`/registration/success`);
                },
                onError: () => {
                    this.paying = false;
                },
                onClose: () => {
                    this.paying = false;
                },
            });
        },
    },
};
</script>

<template>
    <Head>
        <title>Keamanan Akun</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row mt-1">
            <div class="col-md-8 offset-md-2">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h4 class="mb-3">Autentikasi Dua Faktor (2FA)</h4>

                        <div v-if="props.mustEnable" class="alert alert-warning">
                            Akun dengan role Anda diwajibkan mengaktifkan 2FA sebelum bisa mengakses halaman admin lainnya.
                        </div>

                        <div v-if="status === 'confirmed'">
                            <p class="text-success"><i class="fa fa-check-circle"></i> 2FA sudah aktif untuk akun ini.</p>

                            <button class="btn btn-outline-secondary me-2" @click="loadRecoveryCodes" :disabled="loading">
                                Lihat Recovery Codes
                            </button>
                            <button class="btn btn-outline-danger" @click="disable" :disabled="loading">
                                Nonaktifkan 2FA
                            </button>

                            <div v-if="recoveryCodes.length" class="mt-3">
                                <p>Simpan recovery codes ini di tempat aman. Setiap kode hanya bisa dipakai sekali.</p>
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="code in recoveryCodes" :key="code">{{ code }}</li>
                                </ul>
                            </div>
                        </div>

                        <div v-else-if="status === 'pending-confirmation'">
                            <p>Pindai QR code berikut dengan aplikasi authenticator (Google Authenticator, Authy, dll), lalu masukkan kode yang muncul untuk mengonfirmasi.</p>
                            <div v-if="qrCode" v-html="qrCode" class="mb-3"></div>
                            <p v-if="secretKey">Atau masukkan kode ini secara manual: <code>{{ secretKey }}</code></p>

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <input type="text" inputmode="numeric" class="form-control mb-2" v-model="confirmCode" placeholder="Kode 6 digit">
                                    <div v-if="error" class="alert alert-danger">{{ error }}</div>
                                    <button class="btn btn-primary w-100" @click="confirm" :disabled="loading">Konfirmasi</button>
                                </div>
                            </div>
                        </div>

                        <div v-else>
                            <p>2FA belum diaktifkan untuk akun ini.</p>
                            <button class="btn btn-primary" @click="enable" :disabled="loading">Aktifkan 2FA</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    enabled: { type: Boolean, default: false },
    confirmed: { type: Boolean, default: false },
    mustEnable: { type: Boolean, default: false },
});

const status = ref(props.confirmed ? 'confirmed' : (props.enabled ? 'pending-confirmation' : 'disabled'));
const qrCode = ref('');
const secretKey = ref('');
const confirmCode = ref('');
const recoveryCodes = ref([]);
const error = ref('');
const loading = ref(false);

const loadQrCode = async () => {
    const { data } = await window.axios.get('/user/two-factor-qr-code');
    qrCode.value = data.svg;
    const secret = await window.axios.get('/user/two-factor-secret-key');
    secretKey.value = secret.data.secretKey;
};

const enable = async () => {
    loading.value = true;
    error.value = '';
    try {
        await window.axios.post('/user/two-factor-authentication');
        await loadQrCode();
        status.value = 'pending-confirmation';
    } catch (e) {
        if (e.response?.status === 423) {
            window.location.href = '/user/confirm-password';
            return;
        }
        error.value = 'Gagal mengaktifkan 2FA. Silakan coba lagi.';
    } finally {
        loading.value = false;
    }
};

const confirm = async () => {
    loading.value = true;
    error.value = '';
    try {
        await window.axios.post('/user/confirmed-two-factor-authentication', {
            code: confirmCode.value,
        });
        status.value = 'confirmed';
        await loadRecoveryCodes();
    } catch (e) {
        error.value = e.response?.data?.errors?.code?.[0] ?? 'Kode salah, silakan coba lagi.';
    } finally {
        loading.value = false;
    }
};

const loadRecoveryCodes = async () => {
    loading.value = true;
    try {
        const { data } = await window.axios.get('/user/two-factor-recovery-codes');
        recoveryCodes.value = data;
    } finally {
        loading.value = false;
    }
};

const disable = async () => {
    if (!window.confirm('Yakin ingin menonaktifkan 2FA?')) return;
    loading.value = true;
    try {
        await window.axios.delete('/user/two-factor-authentication');
        status.value = 'disabled';
        recoveryCodes.value = [];
    } catch (e) {
        if (e.response?.status === 423) {
            window.location.href = '/user/confirm-password';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
export default { layout: LayoutAdmin };
</script>

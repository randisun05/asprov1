<template>
    <Head>
        <title>Konfirmasi Tryout</title>
    </Head>

    <div class="confirm-wrapper">
        <Link href="/user/tryouts" class="btn-back">
            ← Kembali
        </Link>

        <div class="confirm-container">
            <div class="confirm-card">
                <h3 class="text-center mb-4">Konfirmasi Tryout</h3>

                <div class="section-title">Detail Peserta</div>
                <table class="detail-table mb-4">
                    <tr>
                        <td>Nama</td>
                        <td>: {{ detail_event.member.name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>: {{ detail_event.member.email }}</td>
                    </tr>
                    <tr>
                        <td>Tryout</td>
                        <td>: {{ detail_event.event.title }}</td>
                    </tr>
                    <tr>
                        <td>Durasi</td>
                        <td>: {{ detail_event.duration / 60000 }} menit</td>
                    </tr>
                </table>

                <hr>

                <div class="section-title">Deskripsi / Peraturan</div>
                <div class="card-content mb-4">
                    <div v-html="detail_event.event.body"></div>
                </div>

                <div class="start-area">
                    <Link v-if="!detail_event.start_time" :href="`/user/tryout-start/${detail_event.id}`" class="btn-start">
                        Mulai Tryout
                    </Link>

                    <button v-else-if="!detail_event.enr_at" class="btn-continue" @click="$inertia.visit(`/user/tryout/${detail_event.id}/1`)">
                        Lanjutkan
                    </button>

                    <button v-else class="btn-finished" disabled>
                        Sudah Dikerjakan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import LayoutUser from "../../../Layouts/Tryout.vue"
import {Head,Link} from "@inertiajs/vue3"

export default{

layout:LayoutUser,

components:{
Head,
Link
},

props:{
detail_event:Object
}

}

</script>
<style scoped>
.confirm-wrapper {
    max-width: 1100px;
    margin: auto;
    padding: 30px;
}

.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 14px;
    background: #eef2ff;
    color: #3730a3;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
}

/* Mengatur wadah agar berada di tengah dan tidak terlalu lebar */
.confirm-container {
    max-width: 650px;
    margin: 0 auto;
}

.confirm-card {
    background: white;
    border-radius: 16px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(0,0,0,.07);
}

.section-title {
    font-weight: 700;
    color: #374151;
    margin-bottom: 10px;
    font-size: 16px;
}

.card-content {
    font-size: 14px;
    line-height: 1.6;
    color: #4b5563;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
}

.detail-table td {
    padding: 10px 0;
    font-size: 15px;
}

.detail-table td:first-child {
    font-weight: 600;
    width: 150px;
    color: #6b7280;
}

.start-area {
    margin-top: 30px;
}

/* BUTTON STYLES */
.btn-start, .btn-continue, .btn-finished {
    display: block;
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: 0.3s;
    border: none;
}

.btn-start { background: #16a34a; color: white; }
.btn-start:hover { background: #15803d; }

.btn-continue { background: #2563eb; color: white; }
.btn-continue:hover { background: #1d4ed8; }

.btn-finished { background: #9ca3af; color: white; cursor: not-allowed; }

hr {
    margin: 25px 0;
    border: 0;
    border-top: 1px solid #f3f4f6;
}
</style>

<template>

    <Head>
        <title>Hasil Tryout</title>
    </Head>

    <div class="result-wrapper">

        <div class="result-card">

            <h3 class="result-title">
                <i class="fa fa-check-circle"></i>
                Ujian Selesai
            </h3>

            <div class="result-grid">

                <div class="result-item">
                    <span>Nama</span>
                    <strong>{{ detail_event.member.name }}</strong>
                </div>

                <div class="result-item">
                    <span>Email</span>
                    <strong>{{ detail_event.member.email }}</strong>
                </div>

                <div class="result-item">
                    <span>Tryout</span>
                    <strong>{{ detail_event.event.title }}</strong>
                </div>

                <div class="result-item">
                    <span>Mulai</span>
                    <strong>{{ detail_event.start_at}}</strong>
                </div>

                <div class="result-item">
                    <span>Selesai</span>
                    <strong>{{ detail_event.end_at }}</strong>
                </div>

                <div class="result-item">
                    <span>Durasi</span>
                    <strong>{{ detail_event.event.duration }} menit</strong>
                </div>

            </div>


            <div v-if="detail_event.event.show_answer == 'Y'" class="result-score">

                <div class="score-box">

                    <div class="score-number">
                        {{ detail_event.grade }}
                    </div>

                    <div class="score-label">
                        Nilai
                    </div>

                </div>

                <div class="correct-box">

                    <div class="correct-number">
                        {{ detail_event.total_correct }}
                    </div>

                    <div class="correct-label">
                        Jawaban Benar
                    </div>

                </div>
</div>
            <div class="text-center mt-4">
                <Link :href="`/user/tryouts`" class="btn btn-primary">
                    Keluar Ujian
                </Link>
            </div>
        </div>

    </div>

</template>
<script>

import LayoutUser from "../../../Layouts/Tryout.vue"
import { Head, Link } from '@inertiajs/vue3'

export default {

    layout: LayoutUser,

    components: {
        Head, Link
    },

    props: {
        detail_event: Object
    }

}

</script>
<style scoped>
.result-wrapper {
    max-width: 900px;
    margin: auto;
    padding: 30px;
}

.result-card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
}

.result-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 25px;
}

.result-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
}

.result-item {
    background: #f9fafb;
    padding: 12px 15px;
    border-radius: 10px;
}

.result-item span {
    display: block;
    font-size: 12px;
    color: #777;
}

.result-item strong {
    font-size: 14px;
}

.result-score {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
}

.score-box,
.correct-box {
    background: #6366f1;
    color: white;
    padding: 20px;
    border-radius: 14px;
    width: 140px;
    text-align: center;
}

.correct-box {
    background: #16a34a;
}

.score-number,
.correct-number {
    font-size: 32px;
    font-weight: bold;
}

.score-label,
.correct-label {
    font-size: 13px;
}

@media(max-width:768px) {

    .result-grid {
        grid-template-columns: 1fr;
    }

    .result-score {
        flex-direction: column;
        align-items: center;
    }

}
</style>

<template>

    <Head>
        <title>Soal {{ page }}</title>
    </Head>

    <div class="exam-wrapper">

        <div class="exam-header">

            <h4>{{ detail_event.event.title }}</h4>

            <div class="timer-box">

                <VueCountdown :time="duration" @progress="handleChangeDuration" @end="showModalEndTimeExam = true"
                    v-slot="{ days, hours, minutes, seconds }">
                    ⏱ {{ (days * 1440) + (hours * 60) + minutes }} : {{ seconds < 10 ? '0' + seconds : seconds }}
                        </VueCountdown>

            </div>

        </div>


        <div class="exam-body">

            <div class="exam-navigator card-exam">

                <div class="navigator-grid">
                    <div v-for="(q, index) in all_questions" :key="index" class="navigator-btn" :class="{
                        'bg-primary text-white': index + 1 == page,
                        'bg-success text-white': q.answer != 0 && index + 1 != page,
                        'bg-danger text-white': q.answer == 0 && index + 1 != page
                    }" @click="clickQuestion(index)">

                        {{ index + 1 }}

                    </div>
                </div>

                <button class="btn-finish" @click="showModalEndExam = true">

                    Selesai Ujian

                </button>

            </div>


            <div class="exam-question card-exam">

                <h5 class="question-number">
                    Soal {{ page }}
                </h5>

                <div v-if="question_active">

                    <div class="question-text" v-html="question_active.question.text"></div>

                    <div class="answer-list">

                        <div v-for="(answer, index) in answer_order.filter(a => question_active.question[optionMap[a]])"
                            :key="index" class="answer-item" :class="{ active: answer == question_active.answer }"
                            @click="submitAnswer(question_active.question_id, answer)">

                            <div class="answer-label">
                                {{ options[index] }}
                            </div>

                            <div class="answer-text">
                                {{ question_active.question[optionMap[answer]] }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>



        </div>


        <div class="exam-footer">

            <button v-if="page > 1" class="btn-nav" @click="prevPage">

                ← Sebelumnya

            </button>

            <button v-if="page < all_questions.length" class="btn-nav" @click="nextPage">

                Selanjutnya →

            </button>

        </div>

    </div>


    <!-- MODAL -->
    <div v-if="showModalEndExam" class="modal-overlay">
        <div class="modal-box animate-pop">
            <div class="modal-icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>

            <h4 class="modal-title">Akhiri Ujian?</h4>
            <p class="modal-text">
                Pastikan semua jawaban Anda sudah terisi. Setelah diakhiri, Anda tidak dapat mengubah jawaban lagi.
            </p>

            <div class="modal-actions">
                <button class="btn-confirm" @click="endExam">
                    Ya, Selesai
                </button>
                <button class="btn-cancel" @click="showModalEndExam = false">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <div v-if="showModalEndTimeExam" class="modal-overlay">
        <div class="modal-box animate-pop">
            <div class="modal-icon text-danger">
                <i class="fa fa-clock"></i>
            </div>

            <h4 class="modal-title">Waktu Telah Habis!</h4>
            <p class="modal-text">
                Maaf, waktu pengerjaan Anda telah selesai. Klik tombol di bawah untuk menyimpan semua jawaban dan
                mengakhiri ujian.
            </p>

            <div class="modal-actions">
                <button class="btn-confirm" @click="endExam">
                    Simpan & Akhiri Ujian
                </button>
            </div>
        </div>
    </div>

</template>
<script>
import LayoutUser from "../../../Layouts/Tryout.vue"
import { Head } from "@inertiajs/vue3"
import { ref } from "vue"
import VueCountdown from "@chenfengyuan/vue-countdown"
import axios from "axios"
// 1. IMPORT INERTIA WAJIB DISINI
import { router } from "@inertiajs/vue3";

export default {
    layout: LayoutUser,
    components: {
        Head,
        VueCountdown
    },

    props: {
        id: Number,
        page: Number,
        detail_event: Object,
        all_questions: Array,
        question_answered: Number,
        question_active: Object,
        answer_order: Array,
    },

    setup(props) {
        let options = ["A", "B", "C", "D", "E"]
        const optionMap = { 1: 'a', 2: 'b', 3: 'c', 4: 'd', 5: 'e' }
        const counter = ref(0)
        const duration = ref(props.detail_event.duration)
        const showModalEndExam = ref(false)
        const showModalEndTimeExam = ref(false)

        const handleChangeDuration = () => {
            duration.value -= 1000
            counter.value++
            if (duration.value > 0 && counter.value % 60 == 0) {
                axios.put(`/user/tryout-duration/update/${props.detail_event.id}`, {
                    duration: duration.value
                })
            }
        }

        // 2. GUNAKAN INERTIA.GET UNTUK PINDAH HALAMAN
        const prevPage = () => {
            // Menggunakan parseInt untuk memastikan operasi matematika
            const prev = parseInt(props.page) - 1;
            router.get(`/user/tryout/${props.id}/${prev}`, {}, { preserveState: true });
        }

        const nextPage = () => {
            // Menggunakan parseInt untuk memastikan operasi matematika
            const next = parseInt(props.page) + 1;
            router.get(`/user/tryout/${props.id}/${next}`, {}, { preserveState: true });
        }

        const clickQuestion = (index) => {
            router.get(`/user/tryout/${props.id}/${index + 1}`, {}, { preserveState: true })
        }

        // 3. GUNAKAN INERTIA.POST AGAR WARNA NAVIGATOR TERUPDATE (PROPS REFRESH)
        const submitAnswer = (question_id, answer) => {
            router.post("/user/tryout-answer", {
                detail_event_id: props.id,
                question_id: question_id,
                answer: answer,
                duration: duration.value
            }, {
                preserveScroll: true,
                preserveState: true
            })
        }

        const endExam = () => {
            router.post("/user/tryout-end", {
                detail_event_id: props.id
            })
        }

        const onTimerEnd = () => {
            showModalEndTimeExam.value = true;
            // Otomatis jalankan endExam setelah 3 detik atau langsung
            setTimeout(() => {
                endExam();
            }, 5000);
        };

        return {
            options, duration, handleChangeDuration,
            prevPage, nextPage, clickQuestion,
            submitAnswer, endExam,
            showModalEndExam, showModalEndTimeExam, optionMap, onTimerEnd
        }


    }
}
</script>
<style>
.exam-wrapper {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

.exam-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.exam-title {
    font-size: 20px;
    font-weight: 600;
}

.timer-box {
    background: #111827;
    color: white;
    padding: 10px 16px;
    border-radius: 10px;
}

.exam-body {
    display: flex;
    gap: 20px;
}

.card-exam {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
}

.exam-question {
    flex: 3;
}

.exam-navigator {
    flex: 1;
}

.question-number {
    font-weight: 600;
    margin-bottom: 15px;
}

.question-text {
    margin-bottom: 20px;
    font-size: 18px;
}

.answer-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.answer-item {
    display: flex;
    gap: 14px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
}

.answer-item:hover {
    background: #eef2ff;
}

.answer-item.active {
    background: #6366f1;
    color: white;
}

.answer-label {
    font-weight: bold;
}

.navigator-title {
    font-weight: 600;
    margin-bottom: 10px;
}

.navigator-progress {
    font-size: 14px;
    margin-bottom: 15px;
}

.navigator-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.navigator-btn {
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
}

/* Sudah Dijawab (Hijau) */
.navigator-btn.answered {
    background: #22c55e;
    /* Warna Hijau */
    color: white;
}

.navigator-btn.active {
    background: #6366f1 !important;
    /* Warna Biru */
    color: white;
    border: 2px solid #312e81;
}

/* Belum Dijawab (Merah) */
.navigator-btn.unanswered {
    background: #ef4444;
    /* Warna Merah */
    color: white;
}

/* Tambahan: Efek hover agar lebih interaktif */
.navigator-btn:hover {
    opacity: 0.8;
}

.btn-finish {
    margin-top: 25px;
    width: 100%;
    /* Menggunakan padding lebih besar agar lebih lebar/tinggi */
    padding: 14px 20px;

    /* Gradasi warna merah agar terlihat lebih modern */
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;

    /* Font style */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 15px;

    /* Border & Radius */
    border: none;
    border-radius: 12px;

    /* Efek bayangan (Shadow) */
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);

    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* Efek saat tombol di-hover */
.btn-finish:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-2px);
    /* Tombol sedikit naik */
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

/* Efek saat tombol diklik */
.btn-finish:active {
    transform: translateY(1px);
    box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2);
}

.question-navigation {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.btn-nav {
    background: #6366f1;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 10px;
    cursor: pointer;
}

/* Overlay Latar Belakang */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(17, 24, 39, 0.8);
    /* Warna gelap transparan */
    backdrop-filter: blur(4px);
    /* Efek blur di belakang modal */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

/* Kotak Modal */
.modal-box {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Animasi Muncul */
.animate-pop {
    animation: modalPop 0.3s ease-out;
}

@keyframes modalPop {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Ikon Peringatan */
.modal-icon {
    font-size: 50px;
    color: #f59e0b;
    /* Warna kuning peringatan */
    margin-bottom: 20px;
}

.modal-title {
    font-weight: 800;
    color: #111827;
    margin-bottom: 10px;
}

.modal-text {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 30px;
    line-height: 1.5;
}

/* Area Tombol */
.modal-actions {
    display: flex;
    flex-direction: column;
    /* Tombol tumpuk agar lebih lebar */
    gap: 12px;
}

.btn-confirm {
    background: #ef4444;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s;
}

.btn-confirm:hover {
    background: #dc2626;
}

.btn-cancel {
    background: #f3f4f6;
    color: #4b5563;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
}

.btn-primary {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
}

.text-danger {
    color: #ef4444 !important;
}

/* Memastikan modal tidak bisa ditutup dengan klik luar jika perlu (opsional) */
.modal-overlay {
    pointer-events: auto;
}
</style>

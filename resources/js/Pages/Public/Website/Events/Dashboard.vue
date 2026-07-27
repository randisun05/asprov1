<template>
     <Head>
        <title>{{ title }}</title>
    </Head>

    <!--page Header-->
    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/events">Events</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--page Header ends-->

    <div class="dashboard">

        <!-- STATS -->

        <div class="stats">

            <div class="card blue">
                <h3>{{ stats.total }}</h3>
                <p>Total Peserta</p>
            </div>

            <div class="card yellow">
                <h3>{{ stats.active }}</h3>
                <p>Sedang Ujian</p>
            </div>

            <div class="card green">
                <h3>{{ stats.finished }}</h3>
                <p>Selesai</p>
            </div>

            <div class="card red">
                <h3>{{ stats.pending }}</h3>
                <p>Belum Mulai</p>
            </div>

        </div>


        <div class="grid">

            <!-- FASTEST -->

            <!-- <div class="panel">

                <h3>Ranking Tercepat</h3>

                <table class="table">

                    <tr v-for="(f, i) in fastest" :key="f.id">

                        <td>{{ i + 1 }}</td>
                        <td>{{ f.member.name }}</td>
                        <td>{{ formatTime(f.duration) }}</td>

                    </tr>

                </table>

            </div> -->


            <!-- TOP SCORE -->

            <div class="panel">

                <h3>Top Score & Tercepat</h3>

                <table class="table">

                    <tr v-for="(s, i) in topScores" :key="s.id">

                        <td>{{ i + 1 }}</td>
                        <td>{{ s.member.name }}</td>
                        <td class="score">{{ s.grade }}</td>
                         <td class="score">{{ s.member.agency }}</td>
                        <td>{{ formatTime(s.duration) }}</td>

                    </tr>

                </table>

            </div>


            <!-- PROGRESS PESERTA -->

            <div class="panel">

                <h3>Progress Peserta</h3>

                <div v-for="p in progressParticipants" :key="p.name" class="progress-item">

                    <span>{{ p.name }}</span>

                    <div class="bar">

                        <div class="fill" :style="{ width: p.progress + '%' }"></div>

                    </div>

                    <span>{{ p.progress }}%</span>

                </div>

            </div>


            <!-- HEATMAP -->

            <div class="panel">

                <h3>Soal Tersulit</h3>

                <div v-for="h in heatmap" :key="h.question" class="heat-row">

                    <span>Soal {{ h.question }}</span>

                    <div class="heat-bar">

                        <div class="heat-fill" :style="{ width: h.rate + '%', background: heatColor(h.rate) }">
                        </div>

                    </div>

                    <span>{{ h.rate }}%</span>

                </div>

            </div>


            <!-- DISTRIBUTION -->

            <div class="panel">

                <h3>Distribusi Nilai</h3>

                <div class="dist">

                    <div v-for="(v, k) in distribution" :key="k">

                        <span>{{ k }}</span>

                        <div class="bar">

                            <div class="fill green" :style="{ width: v * 10 + '%' }"></div>

                        </div>

                        <span>{{ v }}</span>

                    </div>

                </div>

            </div>


            <!-- ACTIVITY -->

            <div class="panel">

                <h3>Live Activity</h3>

                <ul>

                    <li v-for="a in activities" :key="a.time">

                        <strong>{{ a.name }}</strong>
                        {{ a.action }}

                    </li>

                </ul>

            </div>


            <!-- AVG TIME -->

            <div class="panel avg">

                <h3>Average Waktu</h3>

                <p>{{ formatTime(avgTime) }}</p>

            </div>


        </div>

    </div>

</template>

<script>

//import layout
import LayoutWebsite from '../../../../Layouts/Website.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref,
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

export default {
    //layout
    layout: LayoutWebsite,
    props: {
        stats: Object,
        fastest: Array,
        topScores: Array,
        progressParticipants: Array,
        heatmap: Array,
        distribution: Object,
        avgTime: Number,
        activities: Array,
        title: String,
    },

    mounted(){

    this.interval = setInterval(() => {

        router.reload({
            only:[
                'stats',
                'fastest',
                'topScores',
                'progressParticipants',
                'heatmap',
                'distribution',
                'avgTime',
                'activities'
            ]
        })

    },10000)

},

beforeUnmount(){

    clearInterval(this.interval)

},

    methods: {

        formatTime(sec) {

           if(!sec) return "-"

            let m = Math.floor(sec / 60)
            let s = sec % 60

             return m + " menit " + s + " detik"

        },

        heatColor(rate) {

            if (rate < 40) return "#ef4444"

            if (rate < 70) return "#f59e0b"

            return "#22c55e"

        }

    }


}

</script>

<style scoped>
.dashboard {
    padding: 30px;
    max-width: 1300px;
    margin: auto;
}

.title {
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 25px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.card {
    padding: 20px;
    border-radius: 12px;
    color: white;
    text-align: center;
}

.blue {
    background: #3b82f6
}

.yellow {
    background: #f59e0b
}

.green {
    background: #22c55e
}

.red {
    background: #ef4444
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.panel {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.table {
    width: 100%;
}

.table td {
    padding: 6px;
}

.score {
    font-weight: bold;
    color: #3b82f6;
}

.progress-item {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.bar {
    flex: 1;
    background: #eee;
    height: 8px;
    border-radius: 6px;
    overflow: hidden;
}

.fill {
    height: 8px;
    background: #3b82f6;
}

.green {
    background: #22c55e;
}

.heat-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.heat-bar {
    flex: 1;
    background: #eee;
    height: 10px;
    border-radius: 6px;
    overflow: hidden;
}

.heat-fill {
    height: 10px;
}

.avg {
    text-align: center;
    font-size: 22px;
    font-weight: 600;
}

.dist div {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}

ul {
    padding-left: 15px;
}
</style>

<template>

    <Head>
        <title>Tryouts</title>
    </Head>

    <div class="container-fluid px-5 py-4">

        <!-- SEARCH -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form @submit.prevent>
                    <div class="input-group shadow">
                        <input type="text" class="form-control border-0"
                            v-model="search"
                            placeholder="Cari tryout...">

                        <span class="input-group-text border-0 bg-white">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead class="text-center">
                            <tr>
                                <th width="60">No</th>
                                <th>Tryout</th>
                                <th width="120">Nilai</th>
                                <th width="140">Status</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr v-for="(data, index) in filteredEvents" :key="data.id">

                                <!-- ambil detail_event -->
                                <template v-if="true">
                                    <td class="text-center">
                                        {{ index + 1 }}
                                    </td>

                                    <td>
                                        {{ data.title }}
                                    </td>

                                    <!-- NILAI -->
                                    <td class="text-center">
                                        <span v-if="getDetail(data)?.grade">
                                            {{ getDetail(data).grade }}
                                        </span>
                                        <span v-else>-</span>
                                    </td>

                                    <!-- STATUS -->
                                    <td class="text-center">

                                        <span v-if="getDetail(data)?.end_at" class="badge bg-success">
                                            Selesai
                                        </span>

                                        <span v-else-if="isRunning(data)" class="badge bg-primary">
                                            Berlangsung
                                        </span>

                                        <span v-else-if="isNotStarted(data)" class="badge bg-secondary">
                                            Belum Mulai
                                        </span>

                                        <span v-else class="badge bg-danger">
                                            Terlewat
                                        </span>

                                        <a :href="`/events/${data.slug}/dashboard`"  class="badge bg-success">
                                            Dashboard
                                        </a>

                                    </td>

                                    <!-- AKSI -->
                                    <td class="text-center">

                                        <!-- SUDAH SELESAI -->
                                        <button v-if="getDetail(data)?.end_at"
                                            class="btn btn-danger btn-sm w-100"
                                            disabled>
                                            Sudah Dikerjakan
                                        </button>

                                        <!-- BELUM JOIN -->
                                        <button v-else-if="!getDetail(data) && isRunning(data)"
                                            :disabled="loading"
                                            @click="handleStart(data.id)"
                                            class="btn btn-success btn-sm w-100">

                                            {{ loading ? 'Loading...' : 'Kerjakan' }}
                                        </button>

                                        <!-- SUDAH JOIN BELUM MULAI -->
                                        <button v-else-if="getDetail(data) && !getDetail(data).start_at"
                                            :disabled="loading"
                                            @click="handleStart(data.id)"
                                            class="btn btn-success btn-sm w-100">

                                            {{ loading ? 'Loading...' : 'Mulai' }}
                                        </button>

                                        <!-- LANJUTKAN -->
                                        <Link v-else-if="getDetail(data)?.start_at && !getDetail(data)?.end_at"
                                            :href="`/user/tryout/${getDetail(data).id}/1`"
                                            class="btn btn-info btn-sm w-100">

                                            Lanjutkan
                                        </Link>

                                        <!-- BELUM MULAI -->
                                        <button v-else-if="isNotStarted(data)"
                                            class="btn btn-secondary btn-sm w-100"
                                            disabled>
                                            Belum Mulai
                                        </button>

                                        <!-- TERLEWAT -->
                                        <button v-else
                                            class="btn btn-dark btn-sm w-100"
                                            disabled>
                                            Waktu Terlewat
                                        </button>

                                    </td>
                                </template>

                            </tr>

                            <tr v-if="filteredEvents.length === 0">
                                <td colspan="5" class="text-center py-4">
                                    Tidak ada tryout
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>

</template>

<script>

import LayoutUser from "../../../Layouts/User.vue"
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from "vue"
import axios from "axios"
import { router } from "@inertiajs/vue3";

export default {

    layout: LayoutUser,

    components: {
        Head,
        Link
    },

    props: {
        events: Array
    },

    setup(props) {

        const search = ref("")
        const loading = ref(false)

        /*
        FILTER SEARCH
        */
        const filteredEvents = computed(() => {

            if (!search.value) return props.events

            return props.events.filter(e =>
                e.title.toLowerCase().includes(search.value.toLowerCase())
            )

        })

        /*
        AMBIL DETAIL EVENT USER
        */
        const getDetail = (event) => {
            return event.detail_events?.[0] || null
        }

        /*
        FORMAT DATETIME
        */
        const parseDate = (datetime) => {
            return datetime ? new Date(datetime.replace(' ', 'T')) : null
        }

        /*
        STATUS
        */
        const isRunning = (event) => {
            const start = parseDate(event.start_at)
            const end = parseDate(event.end_at)
            if (!start || !end) return false
            const now = new Date()
            return now >= start && now <= end
        }

        const isNotStarted = (event) => {
            const start = parseDate(event.start_at)
            if (!start) return false
            return new Date() < start
        }

        /*
        START TRYOUT
        */
        const handleStart = async (eventId) => {

            if (loading.value) return
            loading.value = true

            try {

                // JOIN
                const joinRes = await axios.post(`/user/events/${eventId}/join`)
                const detailEventId = joinRes.data.detail_event_id

                // GENERATE
                await axios.post(`/user/tryout/${eventId}/generate`)

                // REDIRECT
                router.visit(`/user/tryout-confirmation/${detailEventId}`)

            } catch (error) {
                console.error(error)
                alert("Gagal memulai tryout")
            } finally {
                loading.value = false
            }
        }

        return {
            search,
            filteredEvents,
            getDetail,
            isRunning,
            isNotStarted,
            handleStart,
            loading
        }
    }

}

</script>

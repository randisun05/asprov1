<template>

    <Head>
        <title>Polling</title>
    </Head>
    <div class="px-4 shadow mb-5 mt-5">
        <section id="our-blog">
            <div class="padding_m card">
                <div class="row text-center">
                    <h3>Polling</h3>
                </div>
                <div v-if="!polls.data.length" class="text-center py-5 text-muted">
                    <i class="fa fa-list-alt fa-3x mb-3 d-block" aria-hidden="true"></i>
                    Belum ada polling saat ini.
                </div>
                <div class="row mb-4">
                    <div v-for="poll in polls.data" :key="poll.id" class="col-md-6 mt-4">
                        <div class="card shadow p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="mb-2">{{ poll.question }}</h5>
                                <span class="badge" :class="poll.is_open ? 'bg-success' : 'bg-secondary'">
                                    {{ poll.is_open ? 'Terbuka' : 'Ditutup' }}
                                </span>
                            </div>
                            <p class="text-muted mb-2">{{ poll.options_count }} opsi jawaban</p>
                            <span class="badge mb-3" :class="poll.has_voted ? 'bg-info' : 'bg-warning'" style="width: fit-content;">
                                {{ poll.has_voted ? 'Anda sudah memilih' : 'Belum memilih' }}
                            </span>
                            <Link :href="`/user/polls/${poll.id}`" class="btn btn-primary border-0 shadow">
                                {{ poll.has_voted || !poll.is_open ? 'Lihat Hasil' : 'Ikuti Polling' }}
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <Pagination :links="polls.links" align="center" />
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import LayoutUser from '../../../Layouts/User.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

export default {
    layout: LayoutUser,
    components: {
        Head,
        Link,
        Pagination,
    },
    props: {
        polls: Object,
    },
};
</script>

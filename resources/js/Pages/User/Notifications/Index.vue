<template>
    <Head>
        <title>Notifikasi</title>
    </Head>

    <section id="notifications" class="padding_m mt-4">
        <div class="mx-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Notifikasi</h3>
                <button class="btn btn-sm btn-outline-primary" @click="readAll" v-if="hasUnread">
                    Tandai semua dibaca
                </button>
            </div>

            <div class="card border-0 shadow">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li v-for="notification in notifications.data" :key="notification.id"
                            class="list-group-item d-flex align-items-start"
                            :class="{ 'bg-light': !notification.is_read }"
                            style="cursor: pointer;"
                            @click="open(notification)">
                            <div class="me-3">
                                <span class="badge rounded-circle p-2" :class="typeBadgeClass(notification.type)">
                                    <i class="fa" :class="typeIcon(notification.type)"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span :class="{ 'fw-bold': !notification.is_read }">{{ notification.title }}</span>
                                    <small class="text-muted ms-2">{{ new Date(notification.created_at).toLocaleString('id-ID') }}</small>
                                </div>
                                <p class="mb-0 text-muted small" v-if="notification.body">
                                    {{ notification.body.length > 150 ? notification.body.slice(0, 150) + '...' : notification.body }}
                                </p>
                            </div>
                            <span v-if="!notification.is_read" class="badge bg-primary rounded-circle ms-2" style="width:10px;height:10px;padding:0;"></span>
                        </li>
                        <li v-if="notifications.data.length === 0" class="list-group-item text-center text-muted py-4">
                            Belum ada notifikasi.
                        </li>
                    </ul>
                </div>
            </div>

            <Pagination :links="notifications.links" align="end" />
        </div>
    </section>
</template>

<script>
import LayoutUser from '../../../Layouts/User.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

export default {
    layout: LayoutUser,
    components: { Head, Pagination },
    props: {
        errors: Object,
        notifications: Object,
    },
    setup(props) {
        const hasUnread = computed(() => props.notifications.data.some((n) => !n.is_read));

        const open = (notification) => {
            router.post(`/user/notifications/${notification.id}/read`);
        };

        const readAll = () => {
            router.post('/user/notifications/read-all');
        };

        const typeIcon = (type) => ({
            event: 'fa-calendar-check-o',
            tryout: 'fa-pencil-square-o',
            post: 'fa-newspaper-o',
            merchan: 'fa-shopping-bag',
            announcement: 'fa-bullhorn',
        }[type] ?? 'fa-bell');

        const typeBadgeClass = (type) => ({
            event: 'bg-primary',
            tryout: 'bg-warning',
            post: 'bg-info',
            merchan: 'bg-success',
            announcement: 'bg-danger',
        }[type] ?? 'bg-secondary');

        return { hasUnread, open, readAll, typeIcon, typeBadgeClass };
    },
};
</script>

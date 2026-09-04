<template>

    <Head>
        <title>{{ poll.question }}</title>
    </Head>
    <div class="px-4 shadow mb-5 mt-5">
        <section id="our-blog">
            <div class="padding_m card p-4">
                <div class="mb-2">
                    <Link href="/user/polls" class="btn btn-sm btn-primary border-0 shadow"><i class="fa fa-arrow-left"></i> Kembali</Link>
                </div>
                <h3>{{ poll.question }}</h3>
                <p v-if="poll.description" class="text-muted">{{ poll.description }}</p>
                <span class="badge mb-3" :class="poll.is_open ? 'bg-success' : 'bg-secondary'" style="width: fit-content;">
                    {{ poll.is_open ? 'Terbuka' : 'Ditutup' }}
                </span>

                <form v-if="!hasVoted && poll.is_open" @submit.prevent="submitVote">
                    <div v-for="option in poll.options" :key="option.id" class="form-check mb-2">
                        <input class="form-check-input" type="radio" :id="`option-${option.id}`" :value="option.id" v-model="selectedOption">
                        <label class="form-check-label" :for="`option-${option.id}`">{{ option.option_text }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary border-0 shadow mt-3" :disabled="!selectedOption || voting">
                        Kirim Pilihan
                    </button>
                </form>

                <div v-else>
                    <div v-for="option in poll.options" :key="option.id" class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>
                                {{ option.option_text }}
                                <i v-if="myOptionId === option.id" class="fa fa-check-circle text-success ms-1" title="Pilihan Anda"></i>
                            </span>
                            <span>{{ option.votes_count }} suara ({{ percentage(option.votes_count) }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" :style="{ width: percentage(option.votes_count) + '%' }"></div>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Total {{ totalVotes }} suara</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import LayoutUser from '../../../Layouts/User.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    layout: LayoutUser,
    components: {
        Head,
        Link,
    },
    props: {
        poll: Object,
        hasVoted: Boolean,
        myOptionId: [Number, String],
        totalVotes: Number,
    },
    setup(props) {
        const selectedOption = ref(null);
        const voting = ref(false);

        const submitVote = () => {
            voting.value = true;
            router.post(`/user/polls/${props.poll.id}/vote`, {
                option_id: selectedOption.value,
            }, {
                onFinish: () => { voting.value = false; },
            });
        };

        const percentage = (count) => {
            if (!props.totalVotes) {
                return 0;
            }
            return Math.round((count / props.totalVotes) * 100);
        };

        return { selectedOption, voting, submitVote, percentage };
    },
};
</script>

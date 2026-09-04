<template>
    <Head>
        <title>Ubah Polling</title>
    </Head>
    <div class="container padding px-5 text-black">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row py-4">
                            <div class="col-md-2 col-12 mb-2">
                                <Link href="/admin/polls" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-arrow-left"></i>
                                Kembali</Link>
                            </div>
                        </div>
                        <h3 class="text-center">Ubah Polling</h3>
                        <form @submit.prevent="submit">
                            <div class="row py-4 ms-5">
                                <div class="col-md-11 mb-4">
                                    <span>Pertanyaan</span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" placeholder="Masukan pertanyaan polling" v-model="form.question">
                                    </div>
                                    <div v-if="errors.question" class="alert alert-danger mt-2">{{ errors.question }}</div>
                                </div>

                                <div class="col-md-11 mb-4">
                                    <span>Deskripsi (opsional)</span>
                                    <div class="form-group mt-1">
                                        <textarea class="form-control" rows="3" placeholder="Masukan deskripsi polling" v-model="form.description"></textarea>
                                    </div>
                                    <div v-if="errors.description" class="alert alert-danger mt-2">{{ errors.description }}</div>
                                </div>

                                <div class="col-md-11 mb-2">
                                    <span>Opsi Jawaban</span>
                                </div>
                                <div class="col-md-11 mb-2" v-for="(option, index) in form.options" :key="option.key">
                                    <div class="input-group">
                                        <input type="text" class="form-control" :placeholder="`Opsi ${index + 1}`" v-model="option.option_text">
                                        <span class="input-group-text" v-if="option.votes_count > 0">{{ option.votes_count }} suara</span>
                                        <button type="button" class="btn btn-danger" @click="removeOption(index)"
                                            :disabled="form.options.length <= 2 || option.votes_count > 0"
                                            :title="option.votes_count > 0 ? 'Opsi ini sudah memiliki suara, tidak bisa dihapus' : ''">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="errors.options" class="col-md-11 alert alert-danger mt-2">{{ errors.options }}</div>
                                <div class="col-md-11 mb-4">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" @click="addOption">
                                        <i class="fa fa-plus"></i> Tambah Opsi
                                    </button>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-md btn-primary border-0 shadow me-2">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

export default {
    layout: LayoutAdmin,
    components: { Head, Link },
    props: {
        errors: Object,
        poll: Object,
    },
    setup(props) {
        let nextKey = 0;

        const form = reactive({
            question: props.poll.question,
            description: props.poll.description,
            options: props.poll.options.map((option) => ({
                key: nextKey++,
                id: option.id,
                option_text: option.option_text,
                votes_count: option.votes_count,
            })),
        });

        const addOption = () => {
            form.options.push({ key: nextKey++, id: null, option_text: '', votes_count: 0 });
        };

        const removeOption = (index) => {
            if (form.options.length > 2 && form.options[index].votes_count === 0) {
                form.options.splice(index, 1);
            }
        };

        const submit = () => {
            router.put(`/admin/polls/${props.poll.id}`, {
                question: form.question,
                description: form.description,
                options: form.options.map((option) => ({
                    id: option.id,
                    option_text: option.option_text,
                })),
            });
        };

        return { form, addOption, removeOption, submit };
    },
};
</script>

<style>
.text-black {
    color: black;
}
</style>

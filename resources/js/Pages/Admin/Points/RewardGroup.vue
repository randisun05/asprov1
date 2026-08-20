<template>
    <Head>
        <title>Reward Poin Massal</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row">
            <div class="col-md-12">
                <div class="row py-4">
                    <div class="col-md-2 col-12 mb-2">
                        <Link href="/admin/members" class="btn btn-md btn-primary border-0 shadow w-100"
                            type="button"><i class="fa fa-arrow-left"></i>
                        Kembali</Link>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <h3 class="text-center">Reward Poin Massal</h3>
                    <p class="text-center text-muted">
                        Beri poin yang sama ke banyak anggota sekaligus, berdasarkan NIP.
                    </p>

                    <form @submit.prevent="submit" class="py-4">
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <span class="text-black">Daftar NIP</span>
                                <div class="form-group mt-1">
                                    <textarea class="form-control" rows="8" v-model="form.nips"
                                        placeholder="Masukkan NIP, pisahkan dengan koma atau enter. Contoh:&#10;199001012020121001&#10;199001012020121002, 199001012020121003"></textarea>
                                </div>
                                <div v-if="form.errors.nips" class="alert alert-danger mt-2">
                                    {{ form.errors.nips }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <span class="text-black">Jumlah Poin</span>
                                    <div class="form-group mt-1">
                                        <input type="number" min="1" class="form-control" v-model="form.amount">
                                    </div>
                                    <div v-if="form.errors.amount" class="alert alert-danger mt-2">
                                        {{ form.errors.amount }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <span class="text-black">Keterangan (opsional)</span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" v-model="form.description"
                                            placeholder="Misal: reward peserta workshop Juli">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-md btn-primary border-0 shadow w-100"
                                    :disabled="form.processing">
                                    Beri Poin ke Semua
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';

//import Head and Link from Inertia
import { Head, Link, useForm } from '@inertiajs/vue3';

export default {
    //layout
    layout: LayoutAdmin,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {
        errors: Object,
    },

    setup() {
        const form = useForm({
            nips: '',
            amount: '',
            description: '',
        });

        const submit = () => {
            form.post('/admin/points/reward-group', {
                onSuccess: () => form.reset(),
            });
        };

        return {
            form,
            submit,
        };
    },
};
</script>

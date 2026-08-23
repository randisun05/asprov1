<template>
    <Head>
        <title>Ubah Pengumuman</title>
    </Head>
    <div class="container padding px-5 text-black">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row py-4">
                            <div class="col-md-2 col-12 mb-2">
                                <Link href="/admin/announcements" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-arrow-left"></i>
                                Kembali</Link>
                            </div>
                        </div>
                        <h3 class="text-center">Ubah Pengumuman</h3>
                        <form @submit.prevent="submit">
                            <div class="row py-4 ms-5">
                                <div class="col-md-11 mb-4">
                                    <span>Judul</span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" placeholder="Masukan Judul" v-model="form.title">
                                    </div>
                                    <div v-if="errors.title" class="alert alert-danger mt-2">{{ errors.title }}</div>
                                </div>

                                <div class="col-md-11 mb-4">
                                    <span>Isi Pengumuman</span>
                                    <div class="form-group mt-1">
                                        <textarea class="form-control" rows="6" placeholder="Masukan Isi Pengumuman" v-model="form.body"></textarea>
                                    </div>
                                    <div v-if="errors.body" class="alert alert-danger mt-2">{{ errors.body }}</div>
                                </div>

                                <div class="col-md-11 mb-4">
                                    <span>Tautan (opsional)</span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" placeholder="Contoh: /berita atau https://..." v-model="form.link">
                                    </div>
                                    <div v-if="errors.link" class="alert alert-danger mt-2">{{ errors.link }}</div>
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
        announcement: Object,
    },
    setup(props) {
        const form = reactive({
            title: props.announcement.title,
            body: props.announcement.body,
            link: props.announcement.link,
        });

        const submit = () => {
            router.put(`/admin/announcements/${props.announcement.id}`, {
                title: form.title,
                body: form.body,
                link: form.link,
            });
        };

        return { form, submit };
    },
};
</script>

<style>
.text-black {
    color: black;
}
</style>

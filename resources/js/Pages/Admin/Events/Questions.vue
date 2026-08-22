<template>

    <Head>
        <title>Kelola Soal Tryout</title>
    </Head>
    <div class="container padding px-5 text-black">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row py-4">
                            <div class="col-md-2 col-12 mb-2">
                                <Link :href="`/admin/events/${event.id}`"
                                    class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-arrow-left"></i>
                                Kembali</Link>
                            </div>
                        </div>

                        <h3 class="text-center">Kelola Soal Tryout: {{ event.title }}</h3>
                        <p class="text-center text-muted">
                            Total soal terpasang untuk tryout ini: <strong>{{ attachedTotal }}</strong>
                        </p>

                        <div class="row py-3 ms-5">
                            <div class="col-md-6">
                                <span class="text-black">Pilih Kelompok Soal</span>
                                <select class="form-control" v-model="categoryId" @change="loadCategory">
                                    <option value="" disabled>Pilih Kelompok Soal</option>
                                    <option :value="category.id" v-for="(category, index) in categories" :key="index">
                                        {{ category.title }} ({{ category.questions_count }} soal)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="selectedCategoryId" class="row py-3 ms-5">
                            <div class="col-md-11">
                                <div v-if="questions.length === 0" class="alert alert-warning">
                                    Belum ada soal di kelompok ini.
                                    <Link href="/admin/questions/create">Tambah soal</Link> atau
                                    <Link href="/admin/questions/import">import soal</Link> ke kelompok ini dulu.
                                </div>

                                <template v-else>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="checkAll(true)">Pilih Semua</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="checkAll(false)">Kosongkan</button>
                                    </div>

                                    <div v-for="(question, index) in questions" :key="question.id" class="form-check mb-3 border-bottom pb-2">
                                        <input class="form-check-input" type="checkbox" :id="'q-' + question.id"
                                            :value="question.id" v-model="checkedIds">
                                        <label class="form-check-label" :for="'q-' + question.id">
                                            <span class="fw-bold">{{ index + 1 }}.</span>
                                            <span v-html="question.text"></span>
                                        </label>
                                    </div>

                                    <button type="button" class="btn btn-md btn-primary border-0 shadow" @click="submit">
                                        Simpan Pilihan Soal
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';

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

//import sweet alert2
import Swal from 'sweetalert2';

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
        event: Object,
        categories: Array,
        selectedCategoryId: Number,
        questions: Array,
        attachedTotal: Number,
    },

    //inisialisasi composition API
    setup(props) {

        const categoryId = ref(props.selectedCategoryId || '');

        const checkedIds = ref(
            props.questions.filter(q => q.attached).map(q => q.id)
        );

        const loadCategory = () => {
            router.get(`/admin/events/${props.event.id}/questions`, {
                category_id: categoryId.value,
            });
        }

        const checkAll = (value) => {
            checkedIds.value = value ? props.questions.map(q => q.id) : [];
        }

        const submit = () => {
            router.post(`/admin/events/${props.event.id}/questions/sync`, {
                question_category_id: categoryId.value,
                question_ids: checkedIds.value,
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Soal tryout berhasil diperbarui.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });
        }

        return {
            categoryId,
            checkedIds,
            loadCategory,
            checkAll,
            submit,
        }
    }
}

</script>

<style>
.text-black {
    color: black;
}
</style>

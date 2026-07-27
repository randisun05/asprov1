<template>
    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">

                    <Link href="/admin/posts" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                        class="fa fa-arrow-left"></i>
                    Kembali</Link>
                    </div>
                    <div class="col-md-1 col-12 mb-2">

                        <Link href="/admin/posts/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                            class="fa fa-plus-circle"></i>
                         Tambah</Link>
                    </div>
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/category/create" class="btn btn-md btn-warning border-0 shadow w-100" type="button">
                            <i class="fa fa-plus-circle"></i>  Kategori
                        </Link>
                    </div>

                    <div class="col-md-4 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search" placeholder="masukkan kata kunci dan enter...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive" id="sub">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Titleee</th>
                                        <th class="border-0">Author</th>
                                        <th class="border-0">Kategori</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(post, index) in posts.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (posts.current_page - 1) * posts.per_page }}</td>
                                        <td>{{ post.title }}</td>
                                        <td>{{ post.member_id !== 1 ? post.member.name : 'Administrator' }} </td>
                                        <td>{{ post.category.title }}</td>
                                        <td> <span v-if="post.status === 'approved'" class="badge bg-success" title="disetujui untuk di publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'private'" class="badge bg-warning" title="hanya untuk konsumsi pribadi">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'perlu ada perbaikan'" class="badge bg-warning" title="silakan dicek kembali">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'rejected'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'submission'" class="badge bg-secondary">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'return'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'limited'" class="badge bg-secondary">{{ post.status }}</span></td>
                                        <td class="text-center">
                                            <Link :href="`/admin/posts/${encodeURIComponent(post.slug)}/edit`" class="btn btn-sm btn-warning border-0 shadow me-2" type="button" title="edit"><i class="fa fa-pencil"></i></Link>
                                            <Link :href="`/admin/posts/${encodeURIComponent(post.slug)}`" class="btn btn-sm btn-info border-0 shadow me-2" type="button"><i class="fa fa-eye" title="lihat detail"></i></Link>
                                            <Link  v-if="post.status !== 'submission' && post.status !== 'approved' && post.status !== 'limited'" @click.prevent="handleSubmission(post.id)" class="btn btn-sm btn-success border-0 shadow me-2" type="button" title="pengajuan publish"><i class="fa fa-envelope"></i></Link>
                                            <button v-if="post.status !== 'rejected' && post.status !== 'approved' && post.status !== 'limited'" @click.prevent="destroy(post.id)" class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash" title="hapus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="posts.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>




</template>

<script>
    //import layout
    import LayoutAdmin from '../../../Layouts/Admin.vue';

    //import component pagination
    import Pagination from '../../../Components/Pagination.vue';

    //import Heade and Link from Inertia
    import {
        Head,
        Link
    } from '@inertiajs/vue3';

    //import ref from vue
    import {
        ref, watch,
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
            Pagination
        },

        //props
        props: {
            errors: Object,
            posts: Object,
            publishs: Object,
            categories: Object,
            limiteds: Object,
        },


        //inisialisasi composition API
        setup(props) {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/admin/admin/posts', {

                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }

             //define method destroy
             const destroy = (id) => {
                Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda tidak akan dapat mengembalikan ini!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    })
                    .then((result) => {
                        if (result.isConfirmed) {

                            router.delete(`/admin/posts/${id}`);

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Peserta Berhasil Dihapus!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            const handleSubmission = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Post akan diajukan untuk publish!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Submit it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/posts/${id}/submission`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Returned!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }


             // Method to count reactions
                const countReactions = (posts) => {
                    posts.data.forEach(post => {
                        // Ensure post.react is an array before filtering
                        if (Array.isArray(post.react)) {
                            // Filter and count reactions based on react_id
                            const loveReactions = post.react.filter(reaction => reaction.react_id === "1").length;
                            const likeReactions = post.react.filter(reaction => reaction.react_id === "2").length;
                            const viewReactions = post.react.filter(reaction => reaction.react_id === "3").length;

                            // Assign the counts to post.react as an object
                            post.react = {
                                love: loveReactions,
                                like: likeReactions,
                                view: viewReactions,
                            };
                        } else {
                            // If react is not an array, set default counts
                            post.react = {
                                love: 0,
                                like: 0,
                                view: 0,
                            };
                        }
                    });
                }

                // Watch for changes in posts and count reactions whenever it updates
                watch(() => props.posts, (newPosts) => {
                    countReactions(newPosts); // Count reactions whenever posts update
                }, { immediate: true }); // Run the function immediately on first render

            //return
            return {
                search,
                handleSearch,
                countReactions,
                handleSubmission,
                destroy


        }
    }
}

</script>

<style>

</style>

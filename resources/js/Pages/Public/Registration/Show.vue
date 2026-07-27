<template>

    <Head>
        <title>Registrasi Keanggotaan Aspro</title>
    </Head>

 <!--page Header-->
 <section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30"></h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><h3>Konfirmasi Pembayaran</h3></li>

               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->

<section id="registration" class="">
   <div class="container">
      <div class="row d-flex justify-content-center">
         <div class="col-lg-12 col-md-12 col-sm-10">
            <div class="mt-4">
               <form @submit.prevent="submit" class="getin_form border-form" id="paid" >
                  <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 NIP
                        </span>
                        <div class="form-group bottom35 mt-1">
                                <input type="text" class="form-control" v-model="form.nip" disabled>
                            <div v-if="errors.nip" class="alert alert-danger mt-2">
                                {{ errors.nip }}
                            </div>
                        </div>
                     </div>

                    <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Nama
                        </span>
                        <div class="form-group bottom35 mt-1">
                                <input type="text" class="form-control" v-model="form.name" disabled>
                            <div v-if="errors.name" class="alert alert-danger mt-2">
                                {{ errors.name }}
                            </div>
                        </div>
                     </div>

                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Bukti Transfer   ( Bentuk File Image/Pdf )
                        </span>
                        <div class="form-group bottom35 mt-1">
                            <div class="input-group">
                                <input type="file" class="form-control" @change="updateImage" placeholder="Masukan Buti Transfer" accept=".jpg, .pdf, .jpeg, .png, .pdf, .JPG">

                            </div>
                            <div v-if="errors.paid" class="alert alert-danger mt-2">
                                    {{ errors.paid }}
                                </div>
                                <div v-if="errors[0]" class="alert alert-danger mt-2">
                                    {{ errors[0] }}
                                </div>
                            </div>
                     </div>

                     <div class="row d-flex justify-content-center">
                        <button type="submit" class="button btnprimary" style="width: 300px;">Submit</button>
                    </div>

                    <div class="row d-flex justify-content-center mt-3">
                        <a :href="`/registration/paid/${register.id}/payment`" class="button" style="width: 300px;">Atau Bayar Online</a>
                    </div>

                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>


</template>

<script>
    //import layout
    import LayoutWebsite from '../../../Layouts/Website.vue';

    //import Head from Inertia
    import {
        Head,
        Link
    } from '@inertiajs/inertia-vue3';

    //import reactive
    import {
        reactive
    } from 'vue';

    //import sweet alert2
    import Swal from 'sweetalert2';

    //import inertia adapter
    import {
        Inertia
    } from '@inertiajs/inertia';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,
            Link

        },

        //props
        props: {
            errors: Object,
            session: Object,
            register: Object,
        },

        //define composition API
        setup(props) {

            //define form state
            const form = reactive({
                nip: props.register.nip,
                name: props.register.name,
                paid: '',

            });

            //submit method
            const submit = () => {

            // Sending data using Inertia.post
            Inertia.post(`/registration/paid/${props.register.id}`, {
                paid : form.paid
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Konfirmasi Pembayaran Berhasil',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 5000
                    });
                },
            });
        }

        const updateImage = (event) => {
            form.paid = event.target.files[0];
        };


            //return form state and submit method
            return {
                form,
                submit,
                updateImage
            };

        }

    }

</script>

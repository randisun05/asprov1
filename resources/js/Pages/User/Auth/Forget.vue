<template>

    <Head>
        <title>Lupa Password</title>
    </Head>


 <!--page Header-->
 <section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30"></h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><h3>Lupa Password</h3></li>
                 <li class="breadcrumb-item active" aria-current="page"></li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->


<section id="our-blog" class="padding_m text-center">
   <div class="container">
      <div class="row d-flex justify-content-center">
         <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="bglight logincontainer">
               <div v-if="errors.message" class="alert alert-danger mt-2">
                    {{ errors.message }}
                    </div>
                    <div v-if="$page.props.session.error" class="alert alert-danger mt-2">
                    {{ $page.props.session.error }}
                    </div>

                    <div v-if="$page.props.session.success" class="alert alert-success mt-2">
                    {{ $page.props.session.success }}
                    </div>


               <form @submit.prevent="submit" class="getin_form border-form" id="login">
                  <div class="row">
                     <div class="col-md-12 col-sm-12">
                        <div class="form-group bottom35">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="fa fa-id-badge"></i>
                                </span>
                                <input type="text" class="form-control" v-model="form.nip" placeholder="NIP">
                            </div>
                            <div v-if="errors.nip" class="alert alert-danger mt-2">
                            {{ errors.nip }}
                            </div>
                        </div>
                     </div>

                     <div class="col-md-12 col-sm-12">
                        <div class="form-group bottom35">
                            <div class="input-group">
                                 <span class="input-group-text" id="basic-addon1">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="email" placeholder="Email Terdaftar" class="form-control" v-model="form.email">
                            </div>
                            <div v-if="errors.email" class="alert alert-danger mt-2">
                                {{ errors.email }}
                            </div>
                        </div>
                     </div>

                     <div class="col-sm-12">
                        <button type="submit" class="button btnprimary">Reset</button>
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
        Head
    } from '@inertiajs/vue3';

    //import reactive
    import {
        reactive
    } from 'vue';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head
        },

        //props
        props: {
            errors: Object,
            session: Object,


        },

        //define composition API
        setup() {

            //define form state
            const form = reactive({
                nip: '',
                password: '',
            });

            //submit method
            const submit = () => {

                //send data to server
                router.get('/forget-password/email', {
                    //data
                    nip: form.nip,
                    email: form.email,
                });
            }

            //return form state and submit method
            return {
                form,
                submit,
            };

        }

    }

</script>

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
                 <li class="breadcrumb-item"><h3> Registrasi Berhasil</h3></li>
                 <li class="breadcrumb-item active" aria-current="page"></li>
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
            <div class="logincontainer">
               <h3 class="darkcolor bottom35 text-center">Terimakasih {{ form.name }} Telah Melakukan Registrasi</h3>
               <h4 class="darkcolor bottom35 text-left">Kami akan melakukan verifikasi terhadap data anda, mohon untuk mengecek kotak masuk atau spam email anda secara berkala. </h4>
               <h4 class="darkcolor bottom35 text-left"><strong>Waspada terhadap berbagai penipuan yang mengatasnamakan Asosiasi Profesi SDM Aparatur (Aspro SDMA).</strong></h4>

               <h4 class="darkcolor bottom35 text-left">Jika anda mendapatkan kendala silakan <a href="/kontak-kami"><u>hubungi kami.</u></a></h4>
               <div class="text-center mt-4"> <Link :href="'/registration'" class="btn btn-md btn-primary border-0 shadow" type="button">Kembali</Link></div>
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
        Link // tambahkan Link di sini
    } from '@inertiajs/vue3';

    //import reactive
    import {
        reactive
    } from 'vue';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,
            Link // tambahkan Link di sini
        },

        //props
        props: {
            token: String,
            register: Object,

        },

        //define composition API
        setup(props) {

            //define form state
            const form = reactive({
                'name':props.register.name,
                'email':props.register.email

            });


            //return form state and submit method
            return {
                form,
            };

        }

    }

</script>

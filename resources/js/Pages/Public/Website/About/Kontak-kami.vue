<template>

    <Head>
        <title>{{ title }}</title>
    </Head>

 <!--page Header-->
<section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                 <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->


<!-- Contact US -->
<section id="kontak kami">
   <div class="container padding_m">
      <div class="row">
        <!-- <div class="col-md-6 col-sm-12">
           <div class="heading-title heading_space wow fadeInUp" data-wow-delay="300ms">
               <span>Ada pertanyaan, masukan, atau ingin berkolaborasi?</span>
               <h2 class="darkcolor">Kontak Kami</h2>
                 <p>Dengan mengisi formulir pada https://bit.ly/bantusobatasprosdma. Kami akan senang hati membaca/mendengar pertanyaan, masukan, atau keinginan berkolaborasi Anda. Tim kami siap membantu!</p>
            </div>
        </div> -->
         <!-- <div class="col-md-6 col-sm-12 margin_bottom our-address heading_space wow fadeInUp">
           <h5>Alamat Kami</h5>
            <p>Kantor Badan Kepegawaian Negara Pusat, Gedung II, Lantai 6, Jl. Mayor Jendral Sutoyo No.12 4, RT.4/RW.14, Cililitan, Kec. Kramat jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13640 </p>
               <div class="col-md-6 col-sm-12 our-address top20">
                  <h5>Telepon</h5>
                  <p class="bottom15"><span><a href="https://api.whatsapp.com/send/?phone=6281316932936&text&type=phone_number&app_absent=0">Admin Aspro SDMA wa.me/6281316932936</a>
                  </span> </p>
               </div>
               <div class="col-md-6 col-sm-6 our-address top20">
                  <h5>Email Kami</h5>
                  <p class="bottom15"><span>halo.asprosdma@gmail.com</span> </p>
               </div>
         </div> -->
          <div v-html="data.body"></div>
      </div>

   </div>
</section>
<!--Contact US Ends-->
<!--Testimonials Ends-->


</template>

<script>
    //import layout
    import LayoutWebsite from '../../../../Layouts/Website.vue';


    //import reactive
    import {
        reactive
    } from 'vue'

     //import Head from Inertia
     import {
        Head
    } from '@inertiajs/vue3';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,

        },

        //props
        props: {
            title:Object,
            data: Object

        },

         //inisialisasi composition API
         setup() {

            // Method to get the URL of the document
            const getImageUrl = (imageName) => {
                return `/storage/${imageName}`;
            }


            //return
            return {
                getImageUrl,
            }
        }

    }

</script>

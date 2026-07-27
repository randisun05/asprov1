<template>

  
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


<section id="our-testimonial" class="padding_m">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-sm-12 text-center">
            <div><img alt="SEO" src="/assets/images/maintenance.png" style="width: 30%;"></div>
         </div>
      </div>
   </div>
</section>
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
            title:Object

        },
        
       

    }

</script>
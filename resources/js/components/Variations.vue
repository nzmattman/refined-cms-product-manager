<template>
  <div>
    <product-variation v-for="item of variations" :item="item" :key="`type_${item.id}`"></product-variation>
  </div>
</template>

<script>
  export default {

    props: ['variations'],

  }
</script>

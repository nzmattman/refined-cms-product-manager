<template>
  <div>
    <div class="add-to-cart__row">
      <label :for="`variation_${item.id}`" class="form__label">{{ item.name }}</label>
      <input type="hidden" name="variations[]" :value="value"/>
      <select :id="`variation_${item.id}`" class="form__control" v-model="chosen">
        <option :value="option" v-for="option of item.options">{{ option.name }}</option>
      </select>
    </div>

    <product-variation v-if="chosen.options.length" v-for="child of chosen.options" :item="child" :key="`type_${item.id}_${chosen.id}`"></product-variation>

    <template v-if="chosen.price">
      <div class="add-to-cart__row add-to-cart__row--price">
        <template v-if="chosen.sale_price">
          <span class="text--strike">${{ chosen.price | toCurrency }}</span>
          <span>${{ chosen.sale_price | toCurrency }}</span>
        </template>
        <template v-else>
          <span>${{ chosen.price | toCurrency }}</span>
        </template>
      </div>
    </template>
  </div>
</template>

<script>
  export default {

    props: ['item'],

    data() {
      return {
        chosen: null,
        value: ''
      }
    },

    created() {
      this.chosen = this.item.options[0];
    },

    watch: {
      chosen: {
        handler() {
          const data = JSON.stringify({
            id: this.chosen.id,
            name: this.chosen.name
          });
          this.value = data;
          this.$emit('input', data);
        },

        deep: true
      }
    },
  }
</script>

<template>
  <div>
    <div class="cart__no-results" v-if="!items.length">
      Your cart is currently empty.
    </div>
    <div class="cart" v-else>
      <table class="cart__table">
        <thead>
          <tr class="cart__row">
            <th class="cart__cell cart__cell--product">Product</th>
            <th class="cart__cell cart__cell--right">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr class="cart__row" v-for="item of items">
            <td class="cart__cell cart__cell--product">
              <strong>{{ item.product.name }}</strong>
              <div class="cart__quantity">
                <strong>Qty:</strong> {{ item.quantity }}
              </div>
              <ul class="cart__variations" v-if="item.variations.length">
                <li class="cart__variation" v-if="variation.value" v-for="variation of item.variations">
                  <span>{{ variation.value }}</span>
                </li>
              </ul>
            </td>
            <td class="cart__cell cart__cell--right">${{ item.total | toCurrency }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td class="cart__cell"><strong>Sub total: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.sub_total | toCurrency }}</td>
          </tr>
          <tr v-if="totals.discount">
            <td class="cart__cell"><strong>Discount: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.discount | toCurrency }}</td>
          </tr>

          <tr v-if="deliveryOptions.length">
            <td class="cart__cell" colspan="2">
              <div><strong>Delivery: </strong></div>
              <div class="cart__delivery-zone" v-for="zone of deliveryOptions">
                <span class="cart__delivery-zone-name">
                  <input type="radio" name="deliveryZone" :id="`delivery_zone_${zone.id}`" :checked="zone.isChecked" @change="setDelivery(zone)"/>
                  <label :for="`delivery_zone_${zone.id}`">{{ zone.name }}:</label>
                </span>
                <span class="cart__delivery-zone-price">
                  <template v-if="zone.price">${{ zone.price | toCurrency }}</template>
                  <template v-else>Free</template>
                </span>
              </div>
            </td>
          </tr>
          <template v-if="cart.extra_fees.length">
            <tr v-for="fee of cart.extra_fees">
              <td class="cart__cell"><strong>{{ fee.name }}: </strong></td>
              <td class="cart__cell cart__cell--right">${{ fee.total | toCurrency }}</td>
            </tr>
          </template>
          <tr v-if="config.orders.gst.active">
            <td class="cart__cell"><strong>GST <small>{{ config.orders.gst.type === 'inc' ? 'Includes' : '' }}</small>:</strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.gst | toCurrency }}</td>
          </tr>
          <tr>
            <td class="cart__cell"><strong>Total: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.total | toCurrency }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>
</template>

<script>
  import { productEvents, productManager } from '../app';

  export default {

    props: ['cart', 'config', 'path', 'shipping'],

    data() {
      return {
        items: [],
        totals: null,
        delivery: [],
        deliveryOptions: [],
        postcode: null
      }
    },

    created() {
      this.items = [... this.cart.items];
      this.totals = {... this.cart.totals };
      this.delivery = this.shipping.map(zone => {
        zone.isChecked = false;
        return zone;
      });

      const postcodeField = document.querySelector('.cart__field--postcode input');
      if (postcodeField && postcodeField.value) {
        this.postcode = postcodeField.value;
      }

      productEvents.$on('products.checkout.postcode', postcode => {
        this.postcode = postcode;
        this.setZones(postcode);
      });

      if (this.cart.delivery && this.cart.delivery.zone.id) {
        this.setZones(false, this.cart.delivery.zone.id);
      } else {
        const post = this.cart.delivery ? this.cart.delivery.postcode : false;
        this.setZones(post);
      }
    },

    methods: {
      setZones(postcode = false, zoneId = false) {
        this.deliveryOptions = this.delivery.filter(zone => {
          zone.isChecked = false;

          if (!zone.postcodes && !zone.price) {
            return true;
          }

          if (postcode && zone.postcodes) {
            const postcodes = zone.postcodes
              .split(',')
              .map(code => code.trim())
            ;

            if (postcodes.length && postcodes.includes(postcode.trim())) {
              return true;
            }
          }

          if (zoneId && zone.id === zoneId) {
            return true;
          }

          return false;
        });

        this.$nextTick(() => {
          const chosen = zoneId
            ? this.deliveryOptions.find(zone => zone.id === zoneId)
            : this.deliveryOptions[0];

          chosen.isChecked = true;
          this.setDelivery(chosen);
        });
      },

      setDelivery(zone) {

        axios.put(`refined/products/cart/${zone.id}/set-delivery`, {
          postcode: this.postcode
        });
        this.cart.delivery = {
          zone,
          postcode: this.postcode
        };
        this.updateTotals();
      },

      updateTotals() {
        this.totals = productManager.updateTotals(this.items, this.totals, this.cart, this.config);
      }
    }

  }
</script>

<template>
  <div>
    <div class="cart__no-results" v-if="!items.length">
      Your cart is currently empty.
    </div>
    <div class="cart" v-else>
      <div class="cart__coupon">
        <label for="form__coupon" class="form__label">Do you have a coupon?</label>
        <div>
          <input type="text" name="code" placeholder="Coupon code" class="form__control" id="form__coupon" v-model="coupon">
          <button class="button" @click.prevent.stop="applyCoupon()">Apply Coupon</button>
        </div>
      </div>
      <table class="cart__table">
        <thead>
          <tr class="cart__row">
            <th class="cart__cell cart__cell--remove"></th>
            <th class="cart__cell cart__cell--image"></th>
            <th class="cart__cell cart__cell--product">Product</th>
            <th class="cart__cell cart__cell--right">Price</th>
            <th class="cart__cell cart__cell--right">Quantity</th>
            <th class="cart__cell cart__cell--right">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr class="cart__row" v-for="item of items">
            <td class="cart__cell cart__cell--remove"><a href="#" @click.prevent.stop="remove(item)"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></a></td>
            <td class="cart__cell cart__cell--image">
              <a v-if="config.cart.product_link.active && item.product.image" :href="item.product.url">
                <img :src="item.product.image"/>
              </a>
              <img v-if="!config.cart.product_link.active && item.product.image" :src="item.product.image"/>
            </td>
            <td class="cart__cell cart__cell--product">
              <a v-if="config.cart.product_link.active" :href="item.product.url"><strong>{{ item.product.name }}</strong></a>
              <strong v-else>{{ item.product.name }}</strong>
              <ul class="cart__variations" v-if="item.variations && item.variations.length">
                <li class="cart__variation" v-if="variation.value" v-for="variation of item.variations">
                  <strong>{{ variation.name }}:</strong> <span>{{ variation.value }}</span>
                </li>
              </ul>
            </td>
            <td class="cart__cell cart__cell--right">${{ item.price | toCurrency }}</td>
            <td class="cart__cell cart__cell--right"><input type="number" v-model="item.quantity" @keyup="updateTotal(item)" class="cart__input"/></td>
            <td class="cart__cell cart__cell--right">${{ item.total | toCurrency }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
            <td class="cart__cell cart__cell--right"><strong>Sub total: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.sub_total | toCurrency }}</td>
          </tr>
          <tr v-if="cart.discount && totals.discount">
            <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
            <td class="cart__cell cart__cell--right">
              <strong>Discount: </strong>
              <div class="cart__quantity">
                <strong>{{ cart.discount.name }}</strong>
              </div>
            </td>
            <td class="cart__cell cart__cell--right">-${{ totals.discount | toCurrency }}</td>
          </tr>
          <tr v-if="totals.delivery">
            <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
            <td class="cart__cell cart__cell--right"><strong>Delivery: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.delivery | toCurrency }}</td>
          </tr>
          <template v-if="cart.extra_fees.length">
            <tr v-for="fee of cart.extra_fees">
              <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
              <td class="cart__cell cart__cell--right"><strong>{{ fee.name }}: </strong></td>
              <td class="cart__cell cart__cell--right">${{ fee.total | toCurrency }}</td>
            </tr>
          </template>
          <tr v-if="config.orders.gst.active">
            <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
            <td class="cart__cell cart__cell--right"><strong>GST <small>{{ config.orders.gst.type === 'inc' ? 'Includes' : '' }}</small>:</strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.gst | toCurrency }}</td>
          </tr>
          <tr>
            <td class="cart__cell cart__cell--right cart__cell--no-border" colspan="4">&nbsp;</td>
            <td class="cart__cell cart__cell--right"><strong>Total: </strong></td>
            <td class="cart__cell cart__cell--right">${{ totals.total | toCurrency }}</td>
          </tr>
        </tfoot>
      </table>

      <div class="cart__checkout-button">
        <a :href="`${path}/checkout`" class="button">Checkout</a>
      </div>
    </div>

  </div>
</template>

<script>
  import axios from 'axios';
  import { productManager } from '../app';

  export default {

    props: ['cart', 'config', 'path', 'zones'],

    data() {
      return {
        items: [],
        totals: null,
        coupon: ''
      }
    },

    created() {
      this.items = [... this.cart.items];
      this.totals = {... this.cart.totals };
    },

    mounted() {
      this.$nextTick(() => {
        this.setDefaultDelivery();
        this.updateTotals();
      })
    },

    methods: {

      remove(item) {
        if (confirm('Are you sure you want to remove this item?')) {
          axios
            .delete(`refined/products/${item.product.id}/cart/${item.key}`)
            .then(() => {
              this.removeItem(item);
            });
        }
      },

      removeItem(item) {
        const index = this.items.indexOf(item);
        if (index > -1) {
          this.items.splice(index, 1);
          this.updateTotals();
        }
      },

      setDelivery(zone) {

        axios.put(`refined/products/cart/${zone.id}/set-delivery`, {
          postcode: '*'
        });

        this.updateTotals();
      },

      setDefaultDelivery() {
        if (!this.cart.delivery && this.zones) {
          const possibleZones = this.zones.filter(zone => zone.postcodes === '*')
          if (possibleZones.length) {
            const zone = possibleZones[0]
            this.setDelivery(zone);
            this.cart.delivery = {
              zone,
              postcode: '*'
            };
          }
        }
      },

      updateTotal(item) {
        if (item.quantity < 1 && !confirm('A 0 quantity will remove this item, are you sure you want too do this?')) {
          // todo: one day workout how to get the original value
          item.quantity = 1;
        }

        item.total = item.price * parseInt(item.quantity, 10);

        this.setDefaultDelivery();

        axios
          .put(`refined/products/${item.product.id}/cart/update-quantity`, {
            quantity: item.quantity,
            key: item.key
          })
          .then(() => {
            if (item.quantity < 1) {
              this.removeItem(item);
            }
            this.updateTotals();
        });
      },

      applyCoupon() {
        axios
          .post(`refined/products/cart/get-coupon`, {
            coupon: this.coupon
          })
        .then(response => {
          if (response.data) {
            if(response.data.success && response.data.coupon) {
              this.cart.discount = response.data.coupon;
              this.updateTotals();
            } else {
              alert('Sorry, there was an issue applying this coupon');
            }
          }
        })
      },

      updateTotals() {
        this.totals = productManager.updateTotals(this.items, this.totals, this.cart, this.config);
      }
    }

  }
</script>

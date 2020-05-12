window.Vue = require('vue');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

export const productEvents = new Vue({});

import Variations from './components/Variations';
import Variation from './components/Variation';
import Cart from './components/Cart';
import MiniCart from './components/MiniCart';

Vue.component('product-variations', Variations);
Vue.component('product-variation', Variation);
Vue.component('cart', Cart);
Vue.component('mini-cart', MiniCart);

Vue.filter('toCurrency', value => {
  return new Intl.NumberFormat('en-US', {minimumFractionDigits: 2}).format(value || 0)
});

import numeral from 'numeral';
export const productManager = new Vue({
  el: '#product-manager',
  methods: {
    updateTotals(items, totals, cart, config) {
      const newTotals = {... totals};
      const subTotal = numeral(0);

      items.forEach(item => {
        subTotal.add(item.total);
      });

      newTotals.sub_total = subTotal.value();

      const total = numeral(0);
      total.add(subTotal.value());

      if (cart.discount) {
        newTotals.discount = cart.discount.amount;
        total.subtract(cart.discount.amount);
      }

      if (cart.delivery) {
        newTotals.delivery = cart.delivery.zone.price;
        total.add(cart.delivery.zone.price);
      }

      if (config.orders.gst.active) {
        const rate = numeral(config.orders.gst.percent).divide(100).value();
        const gst = numeral(total.value()).multiply(rate).value();
        newTotals.gst = gst;

        if (config.orders.gst.type === 'ex') {
          total.add(gst);
        }
      }

      newTotals.total = total.value();

      return newTotals;
    }
  }
});

const postcodeField = document.querySelector('.cart__field--postcode input');
if (postcodeField) {
  postcodeField.addEventListener('keyup', event => {
    const time = setTimeout(() => {
      productEvents.$emit('products.checkout.postcode', event.target.value);
      clearTimeout(time);
    }, 200);
  });
}

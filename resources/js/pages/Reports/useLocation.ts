import { tryOnMounted, useGeolocation } from '@vueuse/core';
import axios from 'axios';
import { ref, toRef } from 'vue';

export function useLocation(form: any) {
  const { coords } = useGeolocation();
  const address = ref<string>('');
  const error = ref<string | null>(null);

  async function fetchAddress(lat: number, lon: number, form: any) {
    const country = toRef(form.country);
    const city = toRef(form.city);
    const streetAddress = toRef(form.streetAddress);

    try {
      const response = await axios.get('https://nominatim.openstreetmap.org/reverse', {
        params: {
          lat,
          lon,
          format: 'json',
        },
      });
      const { data } = response;

      if (data.address.country) {
        country.value = data.address.country;
      }
      if (data.address.city || data.address.town || data.address.village) {
        city.value = data.address.city || data.address.town || data.address.village;
      }
      if (data.address.road) {
        streetAddress.value = data.address.road;
      }
    } catch (err) {
      console.error(err);
    }
  }

  tryOnMounted(async () => {
    if (!navigator.geolocation) {
      error.value = 'Geolocation is not supported';
      return;
    }
    setTimeout(() => {
      fetchAddress(coords.value.latitude, coords.value.longitude, form);
    }, 100);
  });

  return { coords, address, error, form, fetchAddress };
}

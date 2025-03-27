import { tryOnMounted, useGeolocation } from '@vueuse/core';
import axios from 'axios';
import { ref } from 'vue';

export function useLocation(form: any) {
  const { coords } = useGeolocation();
  const country = ref<string>('');
  const city = ref<string>('');
  const street_address = ref<string>('');
  const address = ref<string>('');
  const error = ref<string | null>(null);

  async function fetchAddress(lat: number, lon: number, form: any) {
    try {
      const response = await axios.get(`https://nominatim.openstreetmap.org/reverse`, {
        params: {
          lat,
          lon,
          format: 'json',
        },
      });
      const data = response.data;
      country.value = data.address.country || '';
      city.value = data.address.city || data.address.town || data.address.village || '';
      street_address.value = data.address.road || '';

      form.setValues({
        country: country.value,
        city: city.value,
        street_address: street_address.value,
      });
    } catch (err) {
      console.error('Error fetching address:', err);
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

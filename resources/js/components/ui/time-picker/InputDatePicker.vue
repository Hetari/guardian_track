<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CalendarIcon } from 'lucide-vue-next';
interface Props {
    placeholder?: string;
    class?: string;
}
const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Escolha uma data',
});
const date = defineModel();
</script>

<template>
    <div>
        <Popover :side="'top'" data-side="top" class=" w-full">
            <PopoverTrigger class="w-full" asChild>
                <Button
                    type="button"
                    variant="outline"
                    :class="cn(date == null ? 'font-normal text-muted-foreground' : '', props.class, 'justify-start')"
                >
                    <CalendarIcon class="mr-2 h-4 w-4" />

                    <span class="overflow-hidden">
                        {{ date?.toLocaleString() || placeholder }}
                    </span>
                </Button>
            </PopoverTrigger>

            <PopoverContent align="center" class="w-auto p-0">
                <Calendar v-model="date" initial-focus />
            </PopoverContent>
        </Popover>
    </div>
</template>
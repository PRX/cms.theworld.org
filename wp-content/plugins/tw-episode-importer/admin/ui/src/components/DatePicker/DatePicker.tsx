"use client"

import type { DayPickerSingleProps } from "react-day-picker"
import React from "react"
import { format } from "date-fns"
import { Calendar as CalendarIcon } from "lucide-react"
import 'react-day-picker/dist/style.css'

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

export type DatePickerProps = Partial<DayPickerSingleProps>;

export function DatePicker(props: DatePickerProps) {
  const { selected } = props;

  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button
          variant={"outline"}
          className={cn(
            "w-[280px] justify-start text-left font-normal",
            !selected && "text-muted-foreground"
          )}
        >
          <CalendarIcon className="mr-2 h-4 w-4" />
          {selected ? format(selected, "PPP") : <span>Pick a date</span>}
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-auto p-0">
        <Calendar
          {...props}
          mode="single"
          initialFocus
        />
      </PopoverContent>
    </Popover>
  )
}

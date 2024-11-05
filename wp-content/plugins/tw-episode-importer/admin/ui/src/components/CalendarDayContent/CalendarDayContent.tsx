import React from "react";
import { useRef } from "react";
import { useDayRender, Day, useDayPicker, ActiveModifiers } from "react-day-picker";
import { Button } from "@/components/ui/button";
import { ArrowBigUp, Check, RefreshCw, Repeat2 } from "lucide-react";

/** Represent the props for the {@link DayContent} component. */
export interface CalendarDayContentProps {
  /** The date representing the day. */
  date: Date;
  /** The month where the day is displayed. */
  displayMonth: Date;
  /** The active modifiers for the given date. */
  activeModifiers: ActiveModifiers;
}

/** Render the content of the day cell. */
export function CalendarDayContent(props: CalendarDayContentProps): JSX.Element {
  const {
    locale,
    formatters: { formatDay }
  } = useDayPicker();
  const { activeModifiers } = props;
  const { loaded, exists, imported, partialyImported, updated, importable } = activeModifiers;

  /** Custom Modifiers:
   * - loaded
   * - exists
   * - imported
   * - partialyImported
   * - updated
   * - importable
   * - playingAudio
  */

  console.log(props);

  return (<>
    {formatDay(props.date, { locale })}
    <span className="absolute left-[-4px] right-[-4px] bottom-[-4px] h-2 flex gap-[2px] items-end justify-end">
      {partialyImported && (
        <span className="w-2 h-2 rounded-full border-2 border-primary"></span>
      )}
      {importable && !partialyImported && (
        <span className="w-3.5 h-3.5 rounded-full bg-primary"></span>
      )}
      {updated && (
        <span className="inline-grid place-items-center w-3.5 h-3.5 rounded-full bg-orange-500 text-white leading-[0]">
          <Repeat2 className="w-3 h-3" />
        </span>
      )}
      {imported && !updated && (
        <span className="inline-grid place-items-center w-3.5 h-3.5 rounded-full bg-green-500 text-white leading-[0]">
          <Check className="w-3 h-3" />
        </span>
      )}
    </span>
  </>);
}

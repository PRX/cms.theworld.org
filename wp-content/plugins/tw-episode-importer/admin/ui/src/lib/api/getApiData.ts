import axios, { CanceledError } from "axios";
import { formatDateKey } from "@/lib/utils/format/formatDateKey";
import { AbortControllerHash, ApiData, ApiEpisode } from "@/types/api";

/**
 * Fetch API data for a give publish date, or date range between publish date and before date.
 * @param publishDate Dovetail publish date of episodes to load.
 * @param beforeDate End fo date range of episodes to load.
 * @param noCache Flag to add cache buster param to request URL.
 * @param controllers Hash of AbortController objects to pass to request options. Valid keys are `'episodes'` and `'segments'`.
 * @returns
 */
export async function getApiData(publishDate?: Date, beforeDate?: Date, noCache?: boolean, controllers?: AbortControllerHash) {
  const date = new Date(publishDate);
  const params = new URLSearchParams( !beforeDate ? {
    on: formatDateKey(date)
  } : {
    after: formatDateKey(date),
    before: formatDateKey(beforeDate)
  })
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const episodesApiUrl = new URL('episodes', apiUrlBase);
  const segmentsApiUrl = new URL('segments', apiUrlBase);
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    }
  };
  const episodesOptions = {
    ...options,
    ...(controllers?.episodes && { signal: controllers.episodes.signal })
  };
  const segmentsOptions = {
    ...options,
    ...(controllers?.segments && { signal: controllers.segments.signal })
  };

  if (noCache) {
    params.set('cb', `${(new Date()).getTime()}`);
  }

  episodesApiUrl.search = params.toString();
  segmentsApiUrl.search = params.toString();

  async function fetchData(): Promise<ApiData> {
    return await Promise.all([
      axios.get<ApiEpisode[]>(episodesApiUrl.toString(), episodesOptions).then((res) => res.status === 200 ? res.data : null),
      axios.get<ApiEpisode[]>(segmentsApiUrl.toString(), segmentsOptions).then((res) => res.status === 200 ? res.data : null),
    ])
    .then((res) => {
      const [episodes, segments] = res;
      return {
        date,
        episodes,
        segments
      }
    })
    .catch((res) => {
      return res instanceof CanceledError ? null : fetchData();
    });
  }

  return fetchData();
}

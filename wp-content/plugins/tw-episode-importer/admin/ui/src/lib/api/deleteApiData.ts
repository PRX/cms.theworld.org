import axios, { CanceledError } from "axios";
import { formatDateKey } from "@/lib/utils/format/formatDateKey";
import { AbortControllerHash, ApiData, ApiEpisode, ApiEpisodeDeleteOptions } from "@/types/api";

/**
 * Delete API data for a given publish date, or date range between publish date and before date.
 * @param publishDate Dovetail publish date of episodes to load.
 * @param beforeDate End fo date range of episodes to load.
 * @param controllers Hash of AbortController objects to pass to request options. Valid keys are `'episodes'` and `'segments'`.
 * @returns
 */
export async function deleteApiData(publishDate?: Date, beforeDate?: Date, controllers?: AbortControllerHash) {
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

  episodesApiUrl.search = params.toString();
  segmentsApiUrl.search = params.toString();

  async function fetchData(): Promise<ApiData> {
    return await Promise.all([
      axios.delete<ApiEpisode[]>(episodesApiUrl.toString(), episodesOptions).then((res) => res.status === 200 ? res.data : null),
      axios.delete<ApiEpisode[]>(segmentsApiUrl.toString(), segmentsOptions).then((res) => res.status === 200 ? res.data : null),
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

/**
 * Delete episode data for a given publish date, or date range between publish date and before date.
 * @param publishDate Dovetail publish date of episodes to load.
 * @param beforeDate End fo date range of episodes to load.
 * @param controller AbortController object to pass to request options.
 * @returns
 */
export async function deleteApiEpisodes(publishDate?: Date, beforeDate?: Date, controller?: AbortController) {
  const date = new Date(publishDate);
  const params = new URLSearchParams( !beforeDate ? {
    on: formatDateKey(date)
  } : {
    after: formatDateKey(date),
    before: formatDateKey(beforeDate)
  })
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL('episodes', apiUrlBase);
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    },
    ...(controller && { signal: controller.signal })
  };

  apiUrl.search = params.toString();

  async function deleteData(): Promise<Partial<ApiData>> {
    return await axios.delete<ApiEpisode[]>(apiUrl.toString(), options).then((res) => res.status === 200 ? res.data : null)
    .then((episodes) => {
      return {
        date,
        episodes
      }
    })
    .catch((res) => {
      return res instanceof CanceledError ? null : deleteData();
    });
  }

  return deleteData();
}

/**
 * Delete segment data for a given publish date, or date range between publish date and before date.
 * @param publishDate Dovetail publish date of episodes to load.
 * @param beforeDate End fo date range of episodes to load.
 * @param controller AbortController object to pass to request options.
 * @returns
 */
export async function deleteApiSegments(publishDate?: Date, beforeDate?: Date, controller?: AbortController) {
  const date = new Date(publishDate);
  const params = new URLSearchParams( !beforeDate ? {
    on: formatDateKey(date)
  } : {
    after: formatDateKey(date),
    before: formatDateKey(beforeDate)
  })
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL('segments', apiUrlBase);
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    },
    ...(controller && { signal: controller.signal })
  };

  apiUrl.search = params.toString();

  async function deleteData(): Promise<Partial<ApiData>> {
    return await axios.delete<ApiEpisode[]>(apiUrl.toString(), options).then((res) => res.status === 200 ? res.data : null)
    .then((segments) => {
      return {
        date,
        segments
      }
    })
    .catch((res) => {
      return res instanceof CanceledError ? null : deleteData();
    });
  }

  return deleteData();
}

/**
 * Delete episode data for a given Dovetail episode.
 * @param id Dovetail ID of episode to delete import data for.
 * @param controller AbortController object to pass to request options.
 * @returns
 */
export async function deleteApiEpisode(id?: string, options?: ApiEpisodeDeleteOptions, controller?: AbortController) {
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL(`episodes/${id}`, apiUrlBase);
  const requestOptions = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    },
    ...(options && { data: options }),
    ...(controller && { signal: controller.signal })
  };

  async function deleteData(): Promise<ApiEpisode> {
    return await axios.delete<ApiEpisode>(apiUrl.toString(), requestOptions).then((res) => res.status === 200 ? res.data : null);
  }

  return deleteData();
}

/**
 * Delete segment data for a given Dovetail episode.
 * @param id Dovetail ID of episode to delete import data for.
 * @param controller AbortController object to pass to request options.
 * @returns
 */
export async function deleteApiSegment(id?: string, options?: ApiEpisodeDeleteOptions, controller?: AbortController) {
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL(`segments/${id}`, apiUrlBase);
  const requestOptions = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    },
    ...(options && { data: options }),
    ...(controller && { signal: controller.signal })
  };

  async function deleteData(): Promise<ApiEpisode> {
    return await axios.delete<ApiEpisode>(apiUrl.toString(), requestOptions).then((res) => res.status === 200 ? res.data : null);
  }

  return deleteData();
}

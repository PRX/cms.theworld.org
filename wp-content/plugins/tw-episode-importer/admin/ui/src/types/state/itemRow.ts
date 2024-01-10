import { ApiAuthor, ApiEpisode, ApiTaxonomy, Maybe } from "@/types/api/api"

export type ItemRowTerm = {
  name: string,
  id?: number,
  taxonomy?: ApiTaxonomy
}

export type ItemRow = {
  guid: string,
  title: string,
  terms: ItemRowTerm[],
  contributors: Maybe<ApiAuthor[]>,
  filename: string,
  duration: string,
  audioUrl: string,
  data: ApiEpisode
}

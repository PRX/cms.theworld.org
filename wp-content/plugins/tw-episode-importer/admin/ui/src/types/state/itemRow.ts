import { ApiAuthor, ApiEpisode, ApiTaxonomy } from "@/types/api/api"
import { Maybe } from "@/types/api/graphql"

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

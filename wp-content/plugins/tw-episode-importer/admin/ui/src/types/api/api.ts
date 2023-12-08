import { Maybe, Post } from "../api/graphql";

export type ApiAuthor = {
  name: string,
  id: Maybe<number>,
  image: Maybe<string>,
}

export type ApiEnclosure = {
  href: string,
  type: string,
  size: number,
  duration: number,
  status: string
}

export type ApiTaxonomy = {
  name: string,
  label: string
}

export type ApiTaxonomies = {
  [k: string]: ApiTaxonomy
}

export type ApiTerm = {
  id: number,
  name: string,
  taxonomy: ApiTaxonomy,
  count: number
}

export type ApiCategory = {
  name: string,
  existingTerms: ApiTerm[]
}

export type ApiEpisode = {
  post?: {
    guid: string,
    databaseId: number,
    audio: {
      databaseId: number,
      url: string
    }
  },
  wasImported: boolean,
  id: string,
  guid: string,
  title: string,
  excerpt: Maybe<string>,
  content: string,
  datePublished: string,
  dateUpdated: Maybe<string>,
  author: Maybe<ApiAuthor>
  enclosure: ApiEnclosure,
  categories: ApiCategory[]
}

export type ApiData = {
  hasImportableItems: boolean,
  hasImportedItems: boolean,
  hasUpdateableItems: boolean,
  episodes: ApiEpisode[],
  segments: ApiEpisode[]
};

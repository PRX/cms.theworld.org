import { Maybe, Post } from "../api/graphql";

export type ApiAuthor = {
  name: string,
  id: Maybe<number>
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
  post: Post,
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
  episodes: ApiEpisode[],
  segments: ApiEpisode[]
};

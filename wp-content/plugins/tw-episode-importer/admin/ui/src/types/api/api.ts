export type Maybe<T> = T | null;

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
  status: string,
  episodeKey: string,
  audioKey: Maybe<string>,
  segment: Maybe<number>,
  version: string
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

export type ApiAudio = {
  guid: string,
  databaseId: number,
  imported: boolean,
  editLink: string,
  datePublished: string,
  dateUpdated: string,
  url: string
}

export type ApiPost = {
  guid: string,
  databaseId: number,
  type: string,
  status: 'publish' | 'future' | 'draft' | 'pending' | 'private' | 'trash' | 'auto-draft' | 'inherit',
  imported: boolean,
  editLink: string,
  datePublished: string,
  dateUpdated: string,
}

export const ApiEpisodeTypes = ['episode', 'segment'] as const;
export type ApiEpisodeType = (typeof ApiEpisodeTypes)[number];

export type ApiEpisode = {
  existingPosts?: ApiPost[],
  existingPost: Maybe<ApiPost>,
  existingAudio: Maybe<ApiAudio>,
  wasImported: boolean,
  hasUpdatedAudio: boolean,
  type: ApiEpisodeType,
  id: string,
  guid: string,
  title: string,
  excerpt: Maybe<string>,
  content: string,
  datePublished: string,
  dateUpdated: Maybe<string>,
  dateBroadcast: Maybe<string>,
  dateKey: Maybe<string>,
  author: Maybe<ApiAuthor[]>
  enclosure: ApiEnclosure,
  categories: ApiCategory[]
}

export type ApiData = {
  date: Date,
  episodes: ApiEpisode[],
  segments: ApiEpisode[]
};

export type ApiEpisodeDeleteOptions = {
  deleteAudio: boolean,
  deleteParent: boolean
};

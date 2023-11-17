/**
 * @file gqlClient.ts
 * Initialize Apollo GraphQL client.
 */

import { ApolloClient, HttpLink, InMemoryCache, from } from '@apollo/client';

const httpLink = new HttpLink({
  uri: `${window.appLocalizer.gqlUrl}`,
  fetchOptions: {
    method: 'GET'
  }
});

export const gqlClient = new ApolloClient({
  cache: new InMemoryCache({
    typePolicies: {
      Post: {
        fields: {
          additionalDates: {
            merge: true
          },
          additionalMedia: {
            merge: true
          },
          presentation: {
            merge: true
          }
        }
      },
      Episode: {
        fields: {
          episodeAudio: {
            merge: true
          },
          episodeDates: {
            merge: true
          },
          episodeContributors: {
            merge: true
          }
        }
      },
      Segment: {
        fields: {
          segmentContent: {
            merge: true
          }
        }
      },
      MediaItem: {
        fields: {
          audioFields: { merge: true }
        }
      },
      Program: {
        fields: {
          posts: { merge: true }
        }
      },
      Contributor: {
        fields: {
          contributorDetails: { merge: true }
        }
      }
    }
  }),
  link: from([httpLink]),
  defaultOptions: {
    query: {
      fetchPolicy: 'no-cache'
    }
  }
});

export default gqlClient;

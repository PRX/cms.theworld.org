/**
 * @file gqlClient.ts
 * Initialize Apollo GraphQL client.
 */

import { ApolloClient, InMemoryCache, createHttpLink, from } from '@apollo/client';
import { setContext }  from '@apollo/client/link/context';

const httpLink = createHttpLink({
  uri: `${window.appLocalizer.gqlUrl}`
});

const authLink = setContext((_, { headers }) => {
  const nonce = window.appLocalizer.nonce;

  return {
    headers: {
      ...headers,
      'X-Wp-Nonce': nonce
    }
  }
})

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
  link: authLink.concat(httpLink),
  defaultOptions: {
    query: {
      fetchPolicy: 'no-cache'
    }
  }
});

export default gqlClient;

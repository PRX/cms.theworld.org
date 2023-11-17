
import React, { useEffect, useState } from 'react';
import { ApiAuthor } from "@/types/api/api"
import { Contributor, Maybe } from "@/types/api/graphql"
import { Skeleton } from '@/components/ui/skeleton';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { gql } from '@apollo/client';
import gqlClient from '@/lib/api/gqlClient';
import { UserPlus2 } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

type ContributorBadgeType = {
  data?: Maybe<ApiAuthor>
}

const getContributor = gql`
  query getContributor($id: ID!) {
    contributor(id: $id, idType: DATABASE_ID) {
      id
      databaseId
      name
      contributorDetails {
        image {
          srcSet
          sourceUrl
          mediaItemUrl
        }
      }
    }
  }
`;

export function ContributorBadge({ data }: ContributorBadgeType) {
  const { name, id } = data || {};
  const [contributor, setContributor] = useState<Contributor>();
  const avatarImageSrc = contributor?.contributorDetails.image?.sourceUrl || contributor?.contributorDetails.image?.mediaItemUrl;

  console.log('ContributorBadge', data);

  useEffect(() => {
    console.log('ContributorBadge >> useEffect', data);

    if (!data?.id) return;

    (async () => {
      const resp = await gqlClient.query<{
        contributor: Maybe<Contributor>;
      }>({
        query: getContributor,
        variables: {
          id: data.id
        }
      });

      console.log(resp?.data);

      if (resp?.data?.contributor) {
        setContributor(resp.data.contributor)
      }
    })()
  }, [data])

  return (
    <div className='inline-flex items-center gap-2 border p-1 pe-4 rounded-full bg-white overflow-hidden whitespace-nowrap overflow-ellipsis'>
      {data ? (
        <>
          <Avatar>
            {id ? (
              <>
                {avatarImageSrc && (
                  <AvatarImage src={avatarImageSrc} alt={name} />
                )}
                <AvatarFallback>{[...(name || '').matchAll(/\b\w/g)].map((match) => match[0]).join('').toUpperCase()}</AvatarFallback>
              </>
            ) : (
              <TooltipProvider>
                <Tooltip>
                  <TooltipTrigger asChild>
                    <AvatarFallback><UserPlus2 className='text-primary' /></AvatarFallback>
                  </TooltipTrigger>
                  <TooltipContent>
                    <p>New Contributor</p>
                  </TooltipContent>
                </Tooltip>
              </TooltipProvider>
            )}
          </Avatar>
          <span>{name}</span>
        </>
      ) : (
        <>
          <Skeleton className='h-[40px] w-[40px] rounded-full' />
          <Skeleton className='h-4 w-[12ch] rounded-full' />
        </>
      )}
    </div>
  )

}

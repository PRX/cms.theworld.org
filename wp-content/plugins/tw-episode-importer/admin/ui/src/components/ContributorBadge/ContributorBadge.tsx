
import React from 'react';
import { ApiAuthor, Maybe } from "@/types/api/api"
import { Skeleton } from '@/components/ui/skeleton';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { UserPlus2 } from 'lucide-react';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

type ContributorBadgeType = {
  data?: Maybe<ApiAuthor>
}

export function ContributorBadge({ data }: ContributorBadgeType) {
  const { name, id, image } = data || {};

  return (
    <div className='inline-flex items-center gap-2 border p-1 pe-4 rounded-full bg-white overflow-hidden whitespace-nowrap overflow-ellipsis'>
      {data ? (
        <>
          <Avatar>
            {id ? (
              <>
                {image && (
                  <AvatarImage src={image} alt={name} />
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

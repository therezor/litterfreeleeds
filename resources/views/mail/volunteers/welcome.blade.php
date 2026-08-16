@component('mail::message')
# Welcome to Litter Free Leeds, {{ $name }}

Thank you for signing up. You have just joined thousands of volunteers across Leeds who go out and keep the city clean for everyone — including our wildlife.

## First, confirm your email address

@component('mail::button', ['url' => $verificationUrl])
Confirm my email address
@endcomponent

This link expires in an hour, and it is also where you choose your password — we didn't ask you for one when you signed up, and we'll never send you one by email.

Nothing else happens until you use it. If the link expires or you'd rather do it later, "forgotten password" on the [sign-in page]({{ $loginUrl }}) gets you there just as well.

## What happens next

@if ($hasBagHolder)
Once you have confirmed your address, your local **Purple Bag Holder** will be in touch. They are a volunteer near you who keeps a stock of the purple bags and hands them out locally. They will sort you out with bags and answer any questions about picking in your area.
@else
Once you have confirmed your address, a **Purple Bag Holder** will be in touch to sort you out with bags. We are still building up our network of bag holders in your area, so this may take a little longer than usual — thank you for your patience.
@endif

## Before your first litter pick

All litter you collect **must** go in the distinctive purple bags. That is how Leeds City Council tells volunteer litter picking apart from waste dumped at the roadside.

**Do**

- Tell a family member or friend where you are going if you are picking alone
- Always wear gloves and use a litter picker
- Wear reflective or light clothing, ideally a hi-vis vest
- Carry a mobile phone in case of emergency
- Only pick on safe-to-access public land
- Wear a strong pair of boots or shoes
- Wash your hands and carry hand sanitiser
- Make sure children are accompanied and supervised by a responsible adult at all times

**Don't**

- Handle needles or sharp objects
- Pick on or near busy or high-speed roads, and never near roads with limits over 40 mph
- Lift large or heavy items
- Lift or collect flytipped waste, or anything where you don't know what the material is
- Pick on private land without specific permission
- Pick in the dark or in bad weather
- Work in or near watercourses

## When your bags are full

Leave them next to a Leeds City Council litter bin wherever you can. If that isn't possible, leave them somewhere a caged wagon can safely pull in — a bus stop or layby, never a junction or a narrow country road — and email <purplebags@leeds.gov.uk> to say exactly where they are.

@component('mail::button', ['url' => $conditionsUrl, 'color' => 'success'])
Read the full conditions of use
@endcomponent

Please read the full conditions before your first pick — they cover collections, equipment loans, private land and what to do about larger items.

## Fancy picking with other people?

@component('mail::panel')
Have a look at the [upcoming community picks]({{ $picksUrl }}) near you. Whether you have five minutes or five hours, every bag counts.
@endcomponent

Thanks again,<br>
{{ config('app.name') }}
@endcomponent

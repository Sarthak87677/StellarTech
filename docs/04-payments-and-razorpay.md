# Payments: Razorpay, UPI, and what you need first

Short answer: **yes, technically — but there's a legal requirement that comes before
any code, and it's worth knowing now rather than after you've built it.**

---

## The thing to know first

Razorpay, PayU, Cashfree, Instamojo — every payment gateway in India — requires
**KYC verification** before it will release money to you. That means submitting:

- PAN card
- Bank account in the **same name** as the account holder
- Address proof
- For a business account: registration documents

**In India, a person under 18 cannot enter into a binding contract**, which means a
minor cannot hold a merchant account in their own name. This isn't a Razorpay quirk —
it's the Indian Contract Act. Every gateway applies it.

So if you're under 18, the account has to belong to an adult or a registered entity.

## What that means practically

You have four realistic routes:

**1. A parent or guardian holds the account.**
Simplest. They complete KYC with their PAN and bank account, and money lands with them.
They're legally responsible for refunds and disputes, so they need to actually agree to
this rather than just hand over documents.

**2. Your school or institution holds it.**
If the club runs under a school, the school may already have a payment setup. Ask the
person who handles school finances. This is often the cleanest route because the money
trail is already accounted for.

**3. Register the club properly.**
A registered society, trust, or Section 8 company can hold a current account and pass
KYC. This is real paperwork — realistically a few weeks and some cost — but it's the
right answer if the club is going to outlive your time at school.

**4. Don't take online payments at all yet.**
Collect workshop fees in person or by direct UPI transfer to an adult's personal
account, and keep the website out of it entirely. For a club running a few workshops a
year, this is genuinely fine and avoids all of the above.

**My honest suggestion:** start with option 4. Add a gateway when the volume actually
justifies the paperwork. A payment button nobody uses is just extra security surface.

---

## About UPI specifically

UPI isn't a separate integration. It's one of the payment methods **inside** Razorpay —
alongside cards, netbanking and wallets. If you integrate Razorpay, you get UPI as a
checkout option automatically. There's nothing extra to build.

You could also just display a UPI ID or QR code on a page and have people pay directly.
That needs no gateway, no KYC beyond a normal bank account, and no code. But:

- No automatic record of who paid for what
- No refund handling
- Nothing verifies the payment actually arrived
- The website can't unlock anything based on it

Fine for "pay ₹200 for the workshop, show me the screenshot." Not fine for anything
automated.

---

## What Razorpay will ask your site for

When you do integrate, Razorpay's review checks that your site has these pages,
publicly reachable:

| Page | Status |
|---|---|
| Terms and Conditions | ✅ built |
| Privacy Policy | ✅ built |
| Contact page with a real address and phone | ⚠️ needs a phone number and postal address added |
| **Refund / Cancellation Policy** | ❌ not written yet |
| **Pricing / what you're selling** | ❌ not written yet |
| Shipping Policy | Not needed — you sell no physical goods |

The refund policy has to be specific: how many days someone has to cancel, how long
refunds take, who to contact. Vague policies get rejected.

---

## Tax, briefly

- Payment gateways report your transactions
- Registration thresholds and GST treatment depend on your entity type and turnover
- Whoever holds the account handles this in their filings

I'm not a tax adviser and this varies by state and entity. If real money starts moving,
the adult holding the account should talk to a CA. For small workshop fees it's usually
straightforward, but it's their name on it, so they should know.

---

## The good news: your site is already built for this

The architecture assumed you'd want payments eventually, so nothing needs restructuring:

**The feature flag exists and is off.** In your `settings` table:

```
payments_enabled  = 0
razorpay_enabled  = 0
```

**Your `.env` already has the slots:**

```
PAYMENTS_ENABLED=false
RAZORPAY_KEY=
RAZORPAY_SECRET=
```

**The credits system is deliberately separate from money.** Credits are a contribution
record — earned by doing club work, never bought. Your Credits page says this explicitly,
and your Terms say credits have no cash value and can't be transferred. Keep that
separation even after you add payments. Mixing "credits you earned" with "credits you
bought" creates a mess: refund questions, fairness complaints, and a system where money
buys standing in the club. That would undermine the streak and contribution system you're
building.

If you sell workshop places later, sell **workshop places** — a separate `orders` table,
its own flow. Don't route it through credits.

---

## When you're ready, here's what gets built

Roughly a day's work, once KYC is approved:

1. `orders` and `payments` tables
2. A products/events table for what you're actually selling
3. Razorpay Checkout on the front end
4. **Server-side signature verification** — this is the security-critical part. Never
   trust the browser's "payment succeeded" message; verify the signature server-side
   against your secret key before marking anything paid.
5. A webhook endpoint for asynchronous confirmations
6. Refund policy page and pricing page
7. Contact page updated with phone and postal address

**One rule that matters more than the rest:** the Razorpay secret key goes in `.env` on
the server and is read only by PHP. It must never appear in JavaScript, in a Blade
template, in git, or in any chat — including with me. The public key is fine in the
browser; the secret is not.

---

## What to do now

1. Get the site live first — payments are a distraction until it works
2. Ask the adult who supervises the club whether they'd hold a merchant account, or
   whether the school already has one
3. If neither, use option 4 and collect fees in person for now
4. When someone's ready to do KYC, come back and I'll build the integration

There's no rush on this. The site works without it, and adding it later costs nothing
extra because the flags and slots are already in place.

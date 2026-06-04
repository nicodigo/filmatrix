export class WatchlistActions {
  #csrfToken;

  constructor(csrfToken) {
    this.#csrfToken = csrfToken;
  }

  async add(titleId, status = 'pending') {
    const res = await fetch('/my-watchlist', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ title_id: titleId, status }),
    });
    const data = await res.json();
    if (!data.success) throw new Error('add failed');
    return data;
  }

  async updateStatus(titleId, status) {
    const res = await fetch('/my-watchlist', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ title_id: titleId, status }),
    });
    const data = await res.json();
    if (!data.success) throw new Error('updateStatus failed');
    return data;
  }

  async remove(titleId) {
    const res = await fetch('/my-watchlist', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ title_id: titleId }),
    });
    const data = await res.json();
    if (!data.success) throw new Error('remove failed');
    return data;
  }
}

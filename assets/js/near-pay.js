window.onload = function () {

  const nearTestNet = new nearApi.Near({
    keyStore: new nearApi.keyStores.BrowserLocalStorageKeyStore(),
    networkId: 'testnet',
    nodeUrl: 'https://rpc.testnet.near.org',
    walletUrl: 'https://wallet.testnet.near.org',
    explorerUrl: "https://explorer.testnet.near.org",
  });
  const nearMainNet = new nearApi.Near({
    keyStore: new nearApi.keyStores.BrowserLocalStorageKeyStore(),
    networkId: 'mainnet',
    nodeUrl: 'https://rpc.near.org',
    walletUrl: 'https://wallet.near.org',
    explorerUrl: "https://explorer.near.org",
  });

  const walletTestNet = new nearApi.WalletConnection(nearTestNet, 'near-app');
  const walletMainNet = new nearApi.WalletConnection(nearMainNet, 'near-app');

  document.querySelectorAll('.near-payment-button').forEach(item => {
    let wallet = (item.dataset.network === 'main') ? walletMainNet : walletTestNet;

    if (!wallet.isSignedIn()) {
      item.textContent = item.dataset.login_text;
    } else {
      item.textContent = item.dataset.text;
    }

    item.addEventListener('click', event => {
      let amount = parseFloat(event.target.dataset.amount);
      if (amount < 0.001) {
        amount = 0.001;
      }

      let payYocto = (amount * 1000).toString() + "000000000000000000000";
      let address = event.target.dataset.address;

      if (wallet.isSignedIn()) {
        wallet.account().sendMoney(address, payYocto);
      } else {
        wallet.requestSignIn(item.dataset.address);
      }
    });
  });

  document.querySelectorAll('.np-sent').forEach(item => {
    let wallet = (item.dataset.network === 'main') ? walletMainNet : walletTestNet;
    let url = `${wallet._near.config.explorerUrl}/transactions/${item.dataset.transaction}`;
    item.setAttribute('href', url);
  });

  document.querySelectorAll('.np-logout').forEach(item => {
    let wallet = (item.dataset.network === 'main') ? walletMainNet : walletTestNet;
    if (wallet.isSignedIn()) {
      item.classList.add('visible');
    }

    item.addEventListener('click', event => {
      wallet.signOut();
      let location = document.location.origin + document.location.pathname;
      document.location.href = location;
    });
  });

};

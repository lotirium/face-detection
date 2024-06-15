ageUrl
function sbmt() {
    const iurl = document.getElementById('iu').value;
    document.getElementById('ii').src = iurl;
    const lin = document.getElementById('li');
    const erm = document.getElementById('em');

    lin.style.display = 'block';
    erm.style.display = 'none';

    fetch('../src/detect.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ iurl })
    })
        .then(response => response.json())
        .then(data => {
            lin.style.display = 'none';
            if (data.success) {
                console.log('Face detection results:', data.data);
                box(data.data);
            } else {
                console.error('Error:', data.error);
                erm.style.display = 'block';
                erm.innerText = `Error: ${data.error}`;
            }
        })
        .catch(error => {
            lin.style.display = 'none';
            console.error('Error:', error);
            erm.style.display = 'block';
            erm.innerText = `Error: ${error.message}`;
        });
}

function box(regions) {
    const image = document.getElementById('ii');
    const imgc = document.querySelector('.id');
    imgc.innerHTML = '';
    imgc.appendChild(image);

    regions.forEach(region => {
        const bbox = region.region_info.bounding_box;
        const box = document.createElement('div');
        box.className = 'bb';
        box.style.left = `${bbox.left_col * 100}%`;
        box.style.top = `${bbox.top_row * 100}%`;
        box.style.right = `${(1 - bbox.right_col) * 100}%`;
        box.style.bottom = `${(1 - bbox.bottom_row) * 100}%`;
        imgc.appendChild(box);
    });
}

function logout() {
    window.location.href = '../src/logout.php';
}

async function tlike(lui) {
    const response = await fetch('../src/toggle_like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            lui
        })
    });
    if (response.ok) {
        const data = await response.json();
        if (data.success) {
            const likeIcon = document.getElementById('like-' + lui);
            if (data.liked) {
                likeIcon.classList.remove('far');
                likeIcon.classList.add('fas');
            } else {
                likeIcon.classList.remove('fas');
                likeIcon.classList.add('far');
            }
        }
    }
}
async function detect() {
    const iurl = document.getElementById('iu').value;
    const response = await fetch('../src/detect.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ iurl: iurl })
    });
    const data = await response.json();
    if (data.success) {
        const count = document.getElementById('scan-count');
        const ccount = parseInt(count.innerText, 10);
        count.innerText = ccount + 1;
    } else {
        alert(data.message || data.error);
    }
}
